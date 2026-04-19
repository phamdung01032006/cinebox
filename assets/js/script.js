// đổi màu thanh navibar bằng cách thêm class vào tên và chỉnh bằng css
$(document).scroll(function() {
    var isScrolled = $(this).scrollTop() > 20;
    $(".topBar").toggleClass("scrolled", isScrolled);
});

// làm cho nút volume nó muted khi bấm vào lúc không mute và mute khi đang không muted
function volumeToggle(button) {
    var muted = $(".previewVideo").prop("muted");
    // set the muted properties to be not muted, and set the not muted to muted
    $(".previewVideo").prop("muted", !muted);
    
    $(button).find("i").toggleClass("fa-solid fa-volume-xmark");
    $(button).find("i").toggleClass("fa-solid fa-volume");
    $(button).toggleClass("active");
}

function previewEnded() {
    $(".previewVideo").toggle();
    $(".previewImage").toggle();
}

function getClientText(key, fallback) {
    if (window.cineboxI18n && window.cineboxI18n[key]) {
        return window.cineboxI18n[key];
    }

    return fallback;
}


// Scroll arrows for entities/videos
$(document).ready(function() {
    function getScrollContainer(buttonOrRow) {
        return buttonOrRow.closest(".category, .season");
    }

    function getScrollableRow(container) {
        return container.find(".entities, .videos").first();
    }

    function checkScrollArrows(row) {
        if (!row.length || !row[0]) return;

        var container = getScrollContainer(row);
        var hasOverflow = row[0].scrollWidth > row.outerWidth();
        container.find(".scroll-arrow").toggleClass("show", hasOverflow);
    }

    function refreshScrollArrows() {
        $(".entities, .videos").each(function() {
            checkScrollArrows($(this));
        });
    }

    $(document).on("click", ".scroll-arrow", function() {
        var container = getScrollContainer($(this));
        var row = getScrollableRow(container);
        var scrollAmount = parseInt($(this).attr("data-scroll"), 10) || 300;
        var delta = $(this).hasClass("left") ? -scrollAmount : scrollAmount;

        row.scrollLeft(row.scrollLeft() + delta);
    });

    setTimeout(refreshScrollArrows, 0);
    $(window).on("load resize", refreshScrollArrows);

    $(document).on("load", ".entities img, .videos img", function() {
        checkScrollArrows($(this).closest(".entities, .videos"));
    });
});


// Thêm tính năng expand trên trang index preview
let popupPlayer = null;

$(document).ready(function () {
    const popupEl = document.getElementById("videoPopupPlayer");
    if (!popupEl) return;

    popupPlayer = new Plyr("#videoPopupPlayer", {
        controls: [
            "restart", "play-large", "rewind", "play", "fast-forward", "progress", "current-time", "duration",
            "mute", "volume", "settings", "pip", "fullscreen",
        ],
        volume: 1
    });
});


$(document).ready(function () {
    const watchEl = document.getElementById("watchPlayer");
    if (!watchEl) return;

    new Plyr("#watchPlayer", {
        controls: [
            "restart", "play-large", "rewind", "play", "fast-forward", "progress", "current-time", "duration",
            "mute", "volume", "settings", "pip", "fullscreen"
        ],
        volume: 1,
        ratio: "16:9"
    });
});

// $(document).ready(function () {
//     // 1. Xác định danh sách controls cho từng loại màn hình
//     const desktopControls = [
//         "restart", "play-large", "rewind", "play", "fast-forward", "progress", "current-time", "duration",
//         "mute", "volume", "settings", "pip", "fullscreen"
//     ];

//     const mobileControls = [
//         "play-large", "play", "progress", "current-time", "mute", "pip", "fullscreen"
//     ];

//     // 2. Hàm kiểm tra xem có phải màn hình nhỏ (mobile) không
//     const isMobile = window.innerWidth <= 800;
//     const activeControls = isMobile ? mobileControls : desktopControls;

//     // 3. Khởi tạo player với bộ controls tương ứng
//     if (document.getElementById("videoPopupPlayer")) {
//         popupPlayer = new Plyr("#videoPopupPlayer", {
//             controls: activeControls,
//             ratio: "16:9"
//         });
//     }

//     if (document.getElementById("watchPlayer")) {
//         new Plyr("#watchPlayer", {
//             controls: activeControls,
//             ratio: "16:9"
//         });
//     }
// });

function openVideoPopup(button) {
    if (!popupPlayer) return;

    var src = $(button).data("src");
    var title = $(button).data("title") || "";

    $("#videoPopupTitle").text(title);

    popupPlayer.source = {
        type: "video",
        sources: [{ src:src, type: "video/mp4" }]
    };

    $("#videoPopup").addClass("show");
    popupPlayer.play();
}

function setWishlistButtonState($button, isActive) {
    const defaultIcon = $button.attr("data-icon-default") || "fa-solid fa-plus";
    const activeIcon = $button.attr("data-icon-active") || "fa-solid fa-check";
    const addTitle = $button.attr("data-title-add") || "Add to wishlist";
    const removeTitle = $button.attr("data-title-remove") || "Remove from wishlist";
    const nextTitle = isActive ? removeTitle : addTitle;
    const nextIcon = isActive ? activeIcon : defaultIcon;

    $button.toggleClass("active", isActive);
    $button.attr("title", nextTitle);
    $button.attr("aria-label", nextTitle);
    $button.attr("aria-pressed", isActive ? "true" : "false");
    $button.find("i").attr("class", nextIcon);
}

function syncWishlistButtons(entityId, isActive) {
    $(".wishlistBtn[data-entity-id='" + entityId + "'], .entityWishlistBtn[data-entity-id='" + entityId + "']").each(function() {
        setWishlistButtonState($(this), isActive);
    });
}

function setWishlistMenuOpen(isOpen) {
    const $menu = $(".wishlistMenu");
    if (!$menu.length) {
        return;
    }

    $menu.toggleClass("open", isOpen);
    $menu.find(".wishlistToggle").attr("aria-expanded", isOpen ? "true" : "false");
    $menu.find(".wishlistDropdown").attr("aria-hidden", isOpen ? "false" : "true");
}

function setMembershipMenuOpen(isOpen) {
    const $menu = $(".membershipMenu");
    if (!$menu.length) {
        return;
    }

    $menu.toggleClass("open", isOpen);
    $menu.find(".membershipTrigger").attr("aria-expanded", isOpen ? "true" : "false");
    $menu.find(".membershipOptions").attr("aria-hidden", isOpen ? "false" : "true");
}

function setMobileNavMenuOpen(isOpen) {
    const $menu = $(".mobileNavMenu");
    if (!$menu.length) {
        return;
    }

    $menu.toggleClass("open", isOpen);
    $menu.find(".mobileNavToggle").attr("aria-expanded", isOpen ? "true" : "false");
    $menu.find(".mobileNavDropdown").attr("aria-hidden", isOpen ? "false" : "true");
}

function updateWishlistCount(count) {
    const $badge = $("#wishlistCountBadge");
    if (!$badge.length) {
        return;
    }

    $badge.text(count);
    $badge.toggleClass("show", count > 0);
}

function refreshWishlistPanel(options) {
    const $body = $("#wishlistDropdownBody");
    if (!$body.length) {
        return;
    }

    const settings = options || {};

    $.get("ajax/getWishlistPanel.php", function(response) {
        if (!response || response.status !== "success") {
            return;
        }

        $body.html(response.itemsHtml || "");
        updateWishlistCount(parseInt(response.count, 10) || 0);

        if (settings.openMenu) {
            setWishlistMenuOpen(true);
        }
    }, "json");
}

function addToWishlist(entityId, button) {
    const $button = $(button);

    if ($button.hasClass("active")) {
        $.post("ajax/removeFromWishlist.php", { entityId: entityId }, function(response) {
            if (!response || response.status !== "success") {
                alert(getClientText("wishlistRemoveError", "Unable to remove this movie from your wishlist right now."));
                return;
            }

            syncWishlistButtons(entityId, false);
            refreshWishlistPanel({ openMenu: $(".wishlistMenu").hasClass("open") });

            if ($(".wishlistPage").length) {
                $(".wishlistPage .entityCard[data-entity-id='" + entityId + "']").fadeOut(180, function() {
                    $(this).remove();

                    if (!$(".wishlistPage .entityCard").length) {
                        $(".wishlistPage .wishlistEntities, .wishlistPage .category .entities").first().replaceWith(
                            "<div class='wishlistEmptyState'><p>" + getClientText("wishlistEmpty", "You haven't added any movies to your wishlist yet.") + "</p></div>"
                        );
                    }
                });
            }
        }, "json").fail(function() {
            alert(getClientText("wishlistRemoveError", "Unable to remove this movie from your wishlist right now."));
        });
        return;
    }

    $.post("ajax/addToWishlist.php", { entityId: entityId }, function(response) {
        if (!response || response.status !== "success") {
            if (response && response.message === "login_required") {
                window.location.href = "login.php";
                return;
            }

            alert(getClientText("wishlistAddError", "Unable to add this movie to your wishlist right now."));
            return;
        }

        syncWishlistButtons(entityId, true);
        refreshWishlistPanel({ openMenu: true });
    }, "json").fail(function() {
        alert(getClientText("wishlistAddError", "Unable to add this movie to your wishlist right now."));
    });
}

$(document).on("click", ".wishlistToggle", function(event) {
    event.preventDefault();
    event.stopPropagation();
    const shouldOpen = !$(this).closest(".wishlistMenu").hasClass("open");
    setWishlistMenuOpen(shouldOpen);
});

$(document).on("click", ".wishlistDropdown", function(event) {
    event.stopPropagation();
});

$(document).on("click", ".membershipTrigger", function(event) {
    event.preventDefault();
    event.stopPropagation();
    const shouldOpen = !$(this).closest(".membershipMenu").hasClass("open");
    setMembershipMenuOpen(shouldOpen);
});

$(document).on("click", ".membershipOptions", function(event) {
    event.stopPropagation();
});

$(document).on("click", ".mobileNavToggle", function(event) {
    event.preventDefault();
    event.stopPropagation();
    const shouldOpen = !$(this).closest(".mobileNavMenu").hasClass("open");
    setMobileNavMenuOpen(shouldOpen);
});

$(document).on("click", ".mobileNavDropdown", function(event) {
    event.stopPropagation();
});

$(document).on("click", ".ratingStar", function() {
    const $button = $(this);
    const $wrapper = $button.closest(".entityRatingStars");
    const entityId = parseInt($wrapper.data("entity-id"), 10);
    const rating = parseInt($button.data("rating"), 10);

    if (!entityId || !rating || $button.is(":disabled")) {
        return;
    }

    $.post("ajax/rateEntity.php", { entityId: entityId, rating: rating }, function(response) {
        if (!response || response.status !== "success") {
            if (response && response.message === "login_required") {
                window.location.href = "login.php";
                return;
            }

            alert(getClientText("ratingSaveError", "Unable to save your rating right now."));
            return;
        }

        $wrapper.find(".ratingStar").each(function() {
            const $star = $(this);
            const starValue = parseInt($star.data("rating"), 10);
            $star.toggleClass("active", starValue <= rating);
        });

        $(".entityUserRatingValue").text(rating + "/5");
        if (response.averageRating) {
            $(".entityAverageRatingValue").text(parseFloat(response.averageRating).toFixed(1) + "/5");
        }
    }, "json").fail(function() {
        alert(getClientText("ratingSaveError", "Unable to save your rating right now."));
    });
});

function closeVideoPopup() {
    if (!popupPlayer) {
        $("#videoPopup").removeClass("show");
        return;
    }

    popupPlayer.pause();
    popupPlayer.stop();
    $("#videoPopup").removeClass("show");
}

// function cho nút mũi tên banner phần xem phim
function goBack() {
    window.history.back();
}

// phần này để nút mũi tên trên ẩn đi sau một thời gian không di chuột
function startHideTimer() {
    var timeout = null;

    $(document).on("mousemove", function() {
        clearTimeout(timeout);
        $(".watchNav").fadeIn();

        timeout = setTimeout(function() {
            $(".watchNav").fadeOut();
        }, 3000);
    })
}

function initVideo(videoId, username) {
    startHideTimer();
    setStartTime(videoId, username);
    updateProgressTimer(videoId, username);
}

// update progress xem phim của user
// hàm cập nhật thời gian
function updateProgressTimer(videoId, username) {
    addDuration(videoId, username);

    var timer;

    $("video").on("playing", function(event) {
        window.clearInterval(timer);
        timer = window.setInterval(function() {
            updateProgress(videoId, username, event.target.currentTime);
        }, 3000);
    })

    .on("ended", function() {
        setFinished(videoId, username);
        window.clearInterval(timer);
    })
}

function addDuration(videoId, username) {
    $.post("ajax/addDuration.php", { videoId: videoId, username:username }, function(data) {
        if(data !== null && data !== "")
            alert(data);
        // trong javascript khi so sánh 1 == "1" thì cho ra True, khi 1 === "1" thì False,
        // nên điều kiện ở đây là vừa khác giá trị vừa khác kiểu dữ liệu
    })
}

function updateProgress(videoId, username, progress) {
    $.post("ajax/updateDuration.php", { videoId: videoId, username:username, progress: progress }, function(data) {
    if(data !== null && data !== "")
        alert(data);
    // trong javascript khi so sánh 1 == "1" thì cho ra True, khi 1 === "1" thì False,
    // nên điều kiện ở đây là vừa khác giá trị vừa khác kiểu dữ liệu
    })
}

// Hàm kiểm tra xem đã xem xong video chưa
function setFinished(videoId, username) {
    $.post("ajax/setFinished.php", { videoId: videoId, username:username }, function(data) {
    if(data !== null && data !== "")
        alert(data);
    // trong javascript khi so sánh 1 == "1" thì cho ra True, khi 1 === "1" thì False,
    // nên điều kiện ở đây là vừa khác giá trị vừa khác kiểu dữ liệu
    })
}

// hàm để bắt đầu video từ đoạn trước đây đã xem đến đó
function setStartTime(videoId, username) {
    $.post("ajax/getProgress.php", { videoId: videoId, username:username }, function(data) {
        if(isNaN(data)) {
            alert(data);
        }

        $("video").on("canplay", function() {
            this.currentTime = data;
            $("video").off("canplay");
        })
    })

}

function watchVideo(videoId) {
        window.location.href = "watch.php?id=" + videoId;
}

function showUpNext() {
    $(".upNext").fadeIn(300);
}

function hideUpNext() {
    $(".upNext").fadeOut(300);
}

// ẩn thanh navbar ở trang xem phim
$(function () {
    // chỉ áp dụng cho trang xem phim
    if (!$(".watchPage").length) return;

    const $episodes = $(".watchEpisodes");
    if (!$episodes.length) return;

    let navVisible = true;

    function setNav(visible) {
        if (visible === navVisible) return;
        navVisible = visible;
        if (visible) showNavBar();
        else hideNavBar();
    }

    // vào trang xem phim thì ẩn nav
    setNav(false);

    let ticking = false;
    $(window).on("scroll", function () {
        if (ticking) return;
        ticking = true;

        requestAnimationFrame(function () {
            const scrollTop = $(window).scrollTop();
            const trigger = $episodes.offset().top - 120; // xuống gần vùng tập thì hiện nav
            setNav(scrollTop >= trigger);
            ticking = false;
        });
    });
});

function hideNavBar() { $(".topBar").addClass("isHidden"); }
function showNavBar() { $(".topBar").removeClass("isHidden"); }

// Tắt dropdown menu khi bấm ra ngoài cửa sổ dropdown
document.addEventListener('click', function (e) {
  if (!e.target.closest('.wishlistMenu')) {
    setWishlistMenuOpen(false);
  }

  if (!e.target.closest('.membershipMenu')) {
    setMembershipMenuOpen(false);
  }

  if (!e.target.closest('.mobileNavMenu')) {
    setMobileNavMenuOpen(false);
  }
});
