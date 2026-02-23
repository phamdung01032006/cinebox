// làm cho nút volume nó muted khi bấm vào lúc không mute và mute khi đang không muted
function volumeToggle(button) {
    var muted = $(".previewVideo").prop("muted");
    // set the muted propeties to be not muted, and set the not muted to muted
    $(".previewVideo").prop("muted", !muted);
    
    $(button).find("i").toggleClass("fa-solid fa-volume-xmark");
    $(button).find("i").toggleClass("fa-solid fa-volume");
    $(button).toggleClass("active");
}

function previewEnded() {
    $(".previewVideo").toggle();
    $(".previewImage").toggle();
}

// Scroll arrows for entities
$(document).ready(function() {
    // Kiểm tra overflow và hiển thị arrows
    function checkScrollArrows(entities, category) {
        var hasOverflow = entities[0].scrollWidth > entities.width();
        if(hasOverflow) {
            category.find('.scroll-arrow.left').addClass('show');
            category.find('.scroll-arrow.right').addClass('show');
        }
    }
    
    // Setup scroll on click
    $('.scroll-arrow').click(function() {
        var category = $(this).closest('.category');
        var entities = category.find('.entities');
        var scrollAmount = 300;
        
        if($(this).hasClass('left')) {
            entities.scrollLeft(entities.scrollLeft() - scrollAmount);
        } else {
            entities.scrollLeft(entities.scrollLeft() + scrollAmount);
        }
    });
    
    // Check overflow khi load
    $('.entities').each(function() {
        var entities = $(this);
        var category = entities.closest('.category');
        checkScrollArrows(entities, category);
    });
});