$(document).ready(function () {
  console.log();
  $(".userLikesVideo").show();
  $(".userDoesNotLikeVideo").show();
  $(".noActionYet").show();

  $(".toogle-likes").on("click", function (e) {
    e.preventDefault();
    const $link = $(e.currentTarget);
    $.ajax({
      method: "POST",
      url: $link.attr("href"),
    }).done(function (data) {
      let number_of_likes_str = null;
      let number_of_likes = null;
      let number_of_dislikes_str = null;
      let number_of_dislikes = null;
      switch (data.action) {
        case "liked":
          number_of_likes_str = $(".number-of-likes-" + data.id);
          number_of_likes =
            parseInt(number_of_likes_str.html().replace(/\D/g, "")) + 1;
          number_of_likes_str.html("(" + number_of_likes + ")");
          $(".likes-video-id-" + data.id).show();
          $(".dislikes-video-id-" + data.id).hide();
          $(".video-id-" + data.id).hide();
          break;
        case "diliked":
          number_of_dislikes_str = $(".number-of-likes-" + data.id);
          number_of_dislikes =
            parseInt(number_of_dislikes_str.html().replace(/\D/g, "")) + 1;
          number_of_dislikes_str.html("(" + number_of_dislikes + ")");
          $(".dislikes-video-id-" + data.id).show();
          $(".likes-video-id-" + data.id).hide();
          $(".video-id-" + data.id).hide();
          break;
        case "undo liked":
          number_of_likes_str = $(".number-of-likes-" + data.id);
          number_of_likes =
            parseInt(number_of_likes_str.html().replace(/\D/g, "")) - 1;
          number_of_likes_str.html("(" + number_of_likes + ")");
          $(".likes-video-id-" + data.id).hide();
          $(".dislikes-video-id-" + data.id).hide();
          $(".video-id-" + data.id).show();
          break;
        case "undo disliked":
          number_of_dislikes_str = $(".number-of-likes-" + data.id);
          number_of_dislikes =
            parseInt(number_of_dislikes_str.html().replace(/\D/g, "")) - 1;
          number_of_dislikes_str.html("(" + number_of_dislikes + ")");
          $(".dislikes-video-id-" + data.id).hide();
          $(".likes-video-id-" + data.id).hide();
          $(".video-id-" + data.id).show();
          break;
      }
    });
  });
});
