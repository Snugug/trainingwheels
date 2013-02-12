{{#if isDirty}}
  <div class="tw-loading"></div>
{{else}}
  <div class="user-summary">
    <div class="user-info">
      {{#linkTo "course.user" user class="ss-icon"}}navigateright{{/linkTo}}
      <h3>{{#linkTo "course.user" user class="user-name"}}{{user_name}}{{/linkTo}}</h3>
      <div class="indicators-summary-users">
        <div {{bindAttr class="css_class_login_status"}}></div>
        <div {{bindAttr class="css_class_resource_overview_status"}}></div>
      </div>
    </div>
  </div>
{{/if}}
