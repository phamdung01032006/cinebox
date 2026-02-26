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
    popupPlayer = new Plyr("#videoPopupPlayer", {
        controls: [
            "play-large", "rewind", "play", "fast-forward", "progress", "current-time", "duration",
            "mute", "volume", "settings", "pip", "fullscreen"
        ],
        ratio: "16:9"
    });
});

function openVideoPopup(button) {
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

function closeVideoPopup() {
    popupPlayer.pause();
    popupPlayer.stop();
    $("#videoPopup").removeClass("show");
}