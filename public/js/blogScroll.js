$(document).ready(function () {
    let offset = 7;
    let loading = false;
  
    $(window).scroll(function () {
      const scrollPercentage =
        ($(window).scrollTop() / ($(document).height() - $(window).height())) *
        100;
  
      if (scrollPercentage > 80 && !loading) {
        loading = true;
        loadMoreBlogs();
      }
    });
  
    function loadMoreBlogs() {
      $("#pagination-loader").html("Loading...");
      let baseUrl = "";
      switch ($("#url").data("url")) {
        case "home":
          if (isSearching) {
            baseUrl = "/search-more-blogs";
          } else {
            baseUrl = "/load-more-blogs";
          }
          break;
        case "Ablogs":
          baseUrl = "/admin/load-more-blogs";
          break;
        case "myBlogs":
          baseUrl = "/blog/load-more-blogs";
          break;
        case "userBlogs":
          baseUrl = "/blog/load-user-blogs/" + $("#user").data("user");
          break;
        case "catBlogs":
          if (isSearching) {
            baseUrl = "/categories/search-more-blogs/" + $("#id").data("id");
          } else {
            baseUrl = "/categories/load-blogs/" + $("#id").data("id");
          }
          break;
      }
      let loadUrl = baseUrl + "?offset=" + offset;

      if (isSearching) {
        loadUrl += "&term=" + searchTerm
      }

      $.get(loadUrl)
        .done(function (response) {
          if (response.trim() !== "") {
            $("#blog-container").append(response);
            offset += 5;
            loading = false;
          } else {
            $("#pagination-loader").html("No more posts to load.");
          }
        })
        .fail(function (error) {
          console.error("Error loading posts:", error);
          $("#pagination-loader").html("An error occurred while loading posts.");
        });
    }
  });
  