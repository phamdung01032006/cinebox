import os
from typing import List

import pandas as pd
from fastapi import FastAPI
from pydantic import BaseModel
from sklearn.metrics.pairwise import cosine_similarity
from sqlalchemy import create_engine, text


DB_HOST = os.getenv("DB_HOST", "localhost")
DB_PORT = os.getenv("DB_PORT", "3306")
DB_NAME = os.getenv("DB_NAME", "cinebox")
DB_USER = os.getenv("DB_USERNAME", "root")
DB_PASSWORD = os.getenv("DB_PASSWORD", "")

DATABASE_URL = (
    f"mysql+pymysql://{DB_USER}:{DB_PASSWORD}@{DB_HOST}:{DB_PORT}/{DB_NAME}?charset=utf8mb4"
)

engine = create_engine(DATABASE_URL, pool_pre_ping=True)
app = FastAPI(title="Cinebox Recommendation API")


class RecommendationResponse(BaseModel):
    userId: int
    entityIds: List[int]
    neighbors: List[int]


def load_interactions() -> pd.DataFrame:
    ratings_query = """
        SELECT u.id AS userId, r.entityId, CAST(r.rating AS DECIMAL(10,2)) AS score
        FROM entityRatings r
        INNER JOIN users u ON u.username = r.username
    """

    wishlist_query = """
        SELECT u.id AS userId, w.entityId, 5.0 AS score
        FROM wishlist w
        INNER JOIN users u ON u.username = w.username
        LEFT JOIN entityRatings r
            ON r.username = w.username
           AND r.entityId = w.entityId
        WHERE r.id IS NULL
    """

    with engine.connect() as connection:
        ratings_df = pd.read_sql(text(ratings_query), connection)
        wishlist_df = pd.read_sql(text(wishlist_query), connection)

    interactions_df = pd.concat([ratings_df, wishlist_df], ignore_index=True)
    if interactions_df.empty:
        return interactions_df

    interactions_df["userId"] = interactions_df["userId"].astype(int)
    interactions_df["entityId"] = interactions_df["entityId"].astype(int)
    interactions_df["score"] = interactions_df["score"].astype(float)

    return (
        interactions_df.groupby(["userId", "entityId"], as_index=False)["score"]
        .max()
        .sort_values(["userId", "entityId"])
    )


def load_seen_entity_ids(user_id: int) -> set[int]:
    seen_query = """
        SELECT DISTINCT v.entityId
        FROM videoprogress vp
        INNER JOIN videos v ON v.id = vp.videoId
        WHERE vp.username = (SELECT username FROM users WHERE id = :user_id)
          AND vp.finished = 1
    """

    wishlist_query = """
        SELECT entityId
        FROM wishlist
        WHERE username = (SELECT username FROM users WHERE id = :user_id)
    """

    rated_query = """
        SELECT entityId
        FROM entityRatings
        WHERE username = (SELECT username FROM users WHERE id = :user_id)
    """

    with engine.connect() as connection:
        seen_df = pd.read_sql(text(seen_query), connection, params={"user_id": user_id})
        wishlist_df = pd.read_sql(text(wishlist_query), connection, params={"user_id": user_id})
        rated_df = pd.read_sql(text(rated_query), connection, params={"user_id": user_id})

    seen_ids = set()
    for frame in (seen_df, wishlist_df, rated_df):
        if not frame.empty:
            seen_ids.update(frame["entityId"].astype(int).tolist())

    return seen_ids


def get_popular_entity_ids(excluded_entity_ids: set[int], limit: int = 20) -> List[int]:
    popularity_query = """
        SELECT ranked.entityId
        FROM (
            SELECT entityId, AVG(score) AS avgScore, COUNT(*) AS totalSignals
            FROM (
                SELECT entityId, CAST(rating AS DECIMAL(10,2)) AS score
                FROM entityRatings
                UNION ALL
                SELECT entityId, 5.0 AS score
                FROM wishlist
            ) interactions
            GROUP BY entityId
        ) ranked
        ORDER BY ranked.avgScore DESC, ranked.totalSignals DESC, ranked.entityId DESC
    """

    with engine.connect() as connection:
        popular_df = pd.read_sql(text(popularity_query), connection)

    if popular_df.empty:
        return []

    entity_ids = [
        int(entity_id)
        for entity_id in popular_df["entityId"].tolist()
        if int(entity_id) not in excluded_entity_ids
    ]

    return entity_ids[:limit]


def get_top_viewed_entity_ids(excluded_entity_ids: set[int], limit: int = 20) -> List[int]:
    top_view_query = """
        SELECT e.id AS entityId, COALESCE(SUM(v.views), 0) AS totalViews
        FROM entities e
        LEFT JOIN videos v ON v.entityId = e.id
        GROUP BY e.id
        ORDER BY totalViews DESC, e.id DESC
    """

    with engine.connect() as connection:
        top_view_df = pd.read_sql(text(top_view_query), connection)

    if top_view_df.empty:
        return []

    entity_ids = [
        int(entity_id)
        for entity_id in top_view_df["entityId"].tolist()
        if int(entity_id) not in excluded_entity_ids
    ]

    return entity_ids[:limit]


def recommend_entity_ids(user_id: int, limit: int = 20) -> tuple[List[int], List[int]]:
    interactions_df = load_interactions()
    seen_entity_ids = load_seen_entity_ids(user_id)

    if interactions_df.empty or user_id not in interactions_df["userId"].values:
        popular_ids = get_popular_entity_ids(seen_entity_ids, limit)
        if popular_ids:
            return popular_ids, []
        return get_top_viewed_entity_ids(seen_entity_ids, limit), []

    user_item_matrix = interactions_df.pivot_table(
        index="userId", columns="entityId", values="score", fill_value=0.0
    )

    similarity_matrix = cosine_similarity(user_item_matrix)
    similarity_df = pd.DataFrame(
        similarity_matrix,
        index=user_item_matrix.index,
        columns=user_item_matrix.index,
    )

    if user_id not in similarity_df.index:
        popular_ids = get_popular_entity_ids(seen_entity_ids, limit)
        if popular_ids:
            return popular_ids, []
        return get_top_viewed_entity_ids(seen_entity_ids, limit), []

    neighbor_series = similarity_df.loc[user_id].drop(labels=[user_id], errors="ignore")
    neighbor_series = neighbor_series[neighbor_series > 0].sort_values(ascending=False).head(5)

    if neighbor_series.empty:
        popular_ids = get_popular_entity_ids(seen_entity_ids, limit)
        if popular_ids:
            return popular_ids, []
        return get_top_viewed_entity_ids(seen_entity_ids, limit), []

    candidate_scores: dict[int, float] = {}
    for neighbor_id, similarity_score in neighbor_series.items():
        neighbor_items = interactions_df[interactions_df["userId"] == int(neighbor_id)]

        for _, row in neighbor_items.iterrows():
            entity_id = int(row["entityId"])
            if entity_id in seen_entity_ids:
                continue

            weighted_score = float(row["score"]) * float(similarity_score)
            candidate_scores[entity_id] = candidate_scores.get(entity_id, 0.0) + weighted_score

    if not candidate_scores:
        popular_ids = get_popular_entity_ids(seen_entity_ids, limit)
        if popular_ids:
            return popular_ids, neighbor_series.index.astype(int).tolist()
        return get_top_viewed_entity_ids(seen_entity_ids, limit), neighbor_series.index.astype(int).tolist()

    recommended_ids = [
        entity_id
        for entity_id, _ in sorted(
            candidate_scores.items(),
            key=lambda item: (-item[1], item[0]),
        )
    ][:limit]

    return recommended_ids, neighbor_series.index.astype(int).tolist()


@app.get("/health")
def healthcheck():
    return {"status": "ok"}


@app.get("/recommend/{user_id}", response_model=RecommendationResponse)
def recommend(user_id: int):
    entity_ids, neighbors = recommend_entity_ids(user_id)
    return RecommendationResponse(userId=user_id, entityIds=entity_ids, neighbors=neighbors)
