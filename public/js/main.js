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

  var catSearchdebounceTimer;
  $("#searchCatInput").on("keyup", function () {
    clearTimeout(catSearchdebounceTimer);
    var $input = $(this);
    catSearchdebounceTimer = setTimeout(function () {
      var searchTerm = $input.val();
      if (searchTerm.length >= 0) {
        searchCatBlogs(searchTerm);
      }
    }, 400);
  });

  function searchCatBlogs(searchTerm) {
    $("#blog-container").empty();
    offset = 0;

    $.get(
      "/categories/search-blogs/" + $("#id").data("id"),
      { term: searchTerm },
      function (response) {
        if (response.html.trim() != "") {
          $("#blog-container").append(response.html);
        } else {
          $("#pagination-loader").html("No matching blogs found.");
        }
      }
    );
  }

  $("#categoryFilter").on("change", function () {
    var selectedCategoryId = $(this).val();
    if (selectedCategoryId) {
      window.location.href = "/categories/blogs/" + selectedCategoryId;
    }
  });

  function loadMoreBlogs() {
    $("#pagination-loader").html("Loading...");
    var baseUrl;
    switch ($("#url").data("url")) {
      case "home":
        var baseUrl = "/load-more-blogs";
        break;
      case "Ablogs":
        var baseUrl = "/admin/load-more-blogs";
        break;
      case "myBlogs":
        var baseUrl = "/blog/load-more-blogs";
        break;
      case "userBlogs":
        var baseUrl = "/blog/load-user-blogs/" + $("#user").data("user");
        break;
      case "catBlogs":
        var baseUrl = "/categories/load-blogs/" + $("#id").data("id");
        break;
    }
    var loadUrl = baseUrl + "?offset=" + offset;
    $.get(loadUrl)
      .done(function (response) {
        if (response.html.trim() !== "") {
          $("#blog-container").append(response.html);
          offset += 5;
          loading = false;
        } else {
          $("#pagination-loader").html("No more blogs to load.");
        }
      })
      .fail(function (error) {
        console.error("Error loading blogs:", error);
        $("#pagination-loader").html("An error occurred while loading blogs.");
      });
  }
});
