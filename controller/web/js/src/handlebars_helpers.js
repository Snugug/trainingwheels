(function($) {
  // From https://gist.github.com/1371586
  //
  // HELPER: #each_with_key
  //
  // Usage: {{#each_with_key container key="myKey"}}...{{/each_with_key}}
  //
  // Iterate over an object containing other objects. Each
  // inner object will be used in turn, with an added key ("myKey")
  // set to the value of the inner object's key in the container.
  Handlebars.registerHelper("each_with_key", function(obj, options) {
    var fn = options.fn;
    var context,
        buffer = "",
        key,
        keyName = options.hash.key;

    for (key in obj) {
      if (obj.hasOwnProperty(key)) {
        context = obj[key];

        if (keyName) {
          context[keyName] = key;
        }

        buffer += fn(context);
      }
    }
    return buffer;
  });

  // HELPER: #key_value
  //
  // Usage: {{#key_value obj}} Key: {{key}} // Value: {{value}} {{/key_value}}
  //
  // Iterate over an object, setting 'key' and 'value' for each property in
  // the object.
  Handlebars.registerHelper("key_value", function(obj, options) {
    var fn = options.fn;
    var buffer = "",
        key;

    for (key in obj) {
      if (obj.hasOwnProperty(key)) {
        buffer += fn({key: key, value: obj[key]});
      }
    }
    return buffer;
  });

})(jQuery);