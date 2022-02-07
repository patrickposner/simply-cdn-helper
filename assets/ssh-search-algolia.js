// Check if we are on the static site.
var current_url = window.location.origin;
var static_url = document.querySelector("meta[name='ssp-url']").getAttribute("content");

if ( static_url.includes(current_url ) ) {
  var baseurl = document.querySelector("meta[name='ssp-config-url']").getAttribute("content");
  var host_name = window.location.hostname;

  let algolia_config_url = baseurl + host_name.split('.').join('-') + '-algolia.json';
  let algolia_config = '';

  function loadIndex(callback) {
    var xobj = new XMLHttpRequest();
    xobj.overrideMimeType("application/json");
    xobj.open('GET', algolia_config_url, false);
    xobj.onreadystatechange = function () {
      if (xobj.readyState == 4 && xobj.status == "200") {
        // Required use of an anonymous callback as .open will NOT return a value but simply returns undefined in asynchronous mode
        callback(xobj.responseText);
      }
    };
    xobj.send(null);
  }

  loadIndex(function (response) {
    algolia_config = JSON.parse(response);
  });

  var client = algoliasearch(algolia_config.app_id, algolia_config.api_key)
  var index = client.initIndex(algolia_config.index);

  var myAutocomplete = autocomplete(algolia_config.selector, { hint: false }, [
    {
      source: autocomplete.sources.hits(index, { hitsPerPage: 10 }),
      displayKey: 'title',
      templates: {
        suggestion: function (suggestion) {
          if (algolia_config.use_excerpt) {
            var sugTemplate = '<a href="' + suggestion.url + '"><span class="search-result-title">' + suggestion.title + '</span><span class="search-result-excerpt">' + suggestion.excerpt + '</span></a>';
          } else {
            var sugTemplate = '<a href="' + suggestion.url + '"><span class="search-result-title">' + suggestion.title + '</span></a>';
          }
          return sugTemplate;
        }
      }
    }
  ])
}
