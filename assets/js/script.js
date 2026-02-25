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

// // Scroll arrows for entities
// $(document).ready(function() {
//     // Kiểm tra overflow và hiển thị/ẩn arrows
//     function checkScrollArrows(entities, category) {
//         var hasOverflow = entities[0].scrollWidth > entities.width();
//         var arrows = category.find('.scroll-arrow');
        
//         if(hasOverflow) {
//             arrows.addClass('show');
//         } else {
//             arrows.removeClass('show');
//         }
//     }
    
//     // Setup scroll on click
//     $('.scroll-arrow').click(function() {
//         var category = $(this).closest('.category');
//         var entities = category.find('.entities');
//         var scrollAmount = 300;
        
//         if($(this).hasClass('left')) {
//             entities.scrollLeft(entities.scrollLeft() - scrollAmount);
//         } else {
//             entities.scrollLeft(entities.scrollLeft() + scrollAmount);
//         }
//     });
    
//     // Check overflow khi load
//     setTimeout(function() {
//         $('.entities').each(function() {
//             var entities = $(this);
//             var category = entities.closest('.category');
//             checkScrollArrows(entities, category);
//         });
//     }, 1000);
    
//     // Check overflow khi window resize
//     $(window).resize(function() {
//         $('.entities').each(function() {
//             var entities = $(this);
//             var category = entities.closest('.category');
//             checkScrollArrows(entities, category);
//         });
//     });
// });

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
            "play-large", "play", "progress", "current-time", "duration",
            "mute", "volume", "settings", "pip", "download", "fullscreen"
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