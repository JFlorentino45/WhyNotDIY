let searchTerm = '';
let isSearching = false;
$(document).ready(function () {
  let debounceTimer;
  $("#searchInput").on("keyup", function () {
    clearTimeout(debounceTimer);
    const $input = $(this);
    isSearching = $input.length > 0;
    debounceTimer = setTimeout(() => {
      searchTerm = $input.val();
      if (searchTerm.length > 0) {
        searchBlogs(searchTerm);
      } else {
        isSearching = false;
        window.location.reload();
      }
    }, 300);
  });
  
  function searchBlogs(searchTerm) {
    $("#blog-container").empty();
    offset = 0;
    let getURL = ""
    
    if ($("#url").data("url") == "home"){
      getURL = "/search-blogs";
    } else {
      getURL = "/categories/search-blogs/" + $("#id").data("id")
    }
    $.get(
      getURL, { term: searchTerm }, function (response) {
      if (response.trim() != "") {
        $("#blog-container").append(response);
      } else {
        $("#pagination-loader").html("No matching posts found.");
      }
    });
  }
  
  $("#categoryFilter").on("change", function () {
    const selectedCategoryId = $(this).val();
    if (selectedCategoryId) {
      window.location.href = "/categories/blogs/" + selectedCategoryId;
    }
  });
});