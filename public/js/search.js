$(document).ready(function () {
    
    var debounceTimer;
    $("#searchInput").on("keyup", function () {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        var searchTerm = $(this).val();
        if (searchTerm.length >= 0) {
          searchBlogs(searchTerm);
        }
      }, 400);
    });
  
    function searchBlogs(searchTerm) {
      $("#blog-container").empty();
      offset = 0;
  
      $.get(
          "/search-blogs", { term: searchTerm }, function (response) {
        if (response.trim() != "") {
          $("#blog-container").append(response);
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
          if (response.trim() != "") {
            $("#blog-container").append(response);
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
});