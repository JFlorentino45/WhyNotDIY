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
          if (response.trim() !== "") {
            $("#blog-container").append(response);
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
  