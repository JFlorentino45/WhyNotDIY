$(document).ready(function () {
  var offset = 7;
  var loading = false;

  $(window).scroll(function () {
    var scrollPercentage =
      ($(window).scrollTop() / ($(document).height() - $(window).height())) *
      100;

    if (scrollPercentage > 80 && !loading) {
      loading = true;
      loadMoreBlogs();
    }
  });

  var debounceTimer;
  $("#searchInput").on("keyup", function () {
    clearTimeout(debounceTimer);
    var $input = $(this);
    debounceTimer = setTimeout(function () {
      var searchTerm = $input.val();
      if (searchTerm.length >= 0) {
        searchBlogs(searchTerm);
      }
    }, 400);
  });

  function searchBlogs(searchTerm) {
    $("#blog-container").empty();
    offset = 0;

    $.get("/search-blogs", { term: searchTerm }, function (response) {
      if (response.html.trim() != "") {
        $("#blog-container").append(response.html);
      } else {
        $("#pagination-loader").html("No matching blogs found.");
      }
    });
  }

  function loadMoreBlogs() {
    $("#pagination-loader").html("Loading...");
    switch (page) {
      case "home":
        var url = "/load-more-blogs";
        break;
      case "Ablogs":
        var url = "/admin/load-more-blogs";
        break;
      case "myBlogs":
        var url = "/blog/load-more-blogs";
        break;
      case "userBlogs":
        var url = "/blog/load-user-blogs/" + userid;
        break;
    }
    $.get(url, { offset: offset }, function (response) {
      if (response.html.trim() != "") {
        $("#blog-container").append(response.html);
        offset += 5;
        loading = false;
      } else {
        $("#pagination-loader").html("No more blogs to load.");
      }
    });
  }
});
