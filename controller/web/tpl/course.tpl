<div class="course-view">
  {{#if title}}
    <div class="course-info">
      <h2>{{title}}</h2>
      <div>
        {{{description}}}
      </div>
      <h2>Machine name</h2>
      <div>
        {{course_name}}
      </div>
    </div>
    <button {{action refreshCourse target="controller"}}>Refresh</button>
    {{outlet instructorOutlet}}
    {{outlet usersOutlet}}
  {{/if}}
</div>
