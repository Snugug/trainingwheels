<div class="resource">
  <div class="resource-info">
    <h4>{{title}}</h4>
    <div class="indicators-summary-users">
      <div {{bindAttr class="css_class_resource_status"}}></div>
    </div>
  </div>
  <div class="resource-details">
    {{#if exists}}
      <dl>
        <dt>Type</dt>
        <dd>{{type}}</dd>
        <dt>Key</dt>
        <dd>{{key}}</dd>
        {{#each attrib in attribsArray}}
          {{#with attrib}}
            <dt>{{title}}</dt>
            <dd>{{value}}</dd>
          {{/with}}
        {{/each}}
      </dl>
    {{else}}
      <div class="resource-attribs-missing">
        Resources not created yet.
      </div>
    {{/if}}
  </div>
</div>
