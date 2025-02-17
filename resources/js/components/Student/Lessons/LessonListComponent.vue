<template>
  <v-simple-table v-if="lessons.length > 0">
    <template v-slot:default>
      <tbody>
      <tr
          v-for="lesson in lessons"
          :key="lesson.id"
      >
        <td class="p-2">
          <img :src="lesson.instructor.avatar" width="70" height="70" class="v-avatar" alt="">
          {{ lesson.instructor.fullName }}
        </td>
        <td>{{ lesson.startAt | dateTime }}</td>
        <td>{{ lesson.endAt | dateTime }}</td>
        <td>
          <v-btn
              small
              color="green accent-2"
              class="text-capitalize text-dark"
              :to="{name:'trainee-lesson-page', params:{lesson:lesson.id}}"
          >
            Details
          </v-btn>
          <v-btn small color="red accent-2" class="text-capitalize text-white" v-if="show_cancel_btn">
            Cancel
          </v-btn>
        </td>
      </tr>
      </tbody>
    </template>
  </v-simple-table>
  <p v-else>There are no Lessons</p>
</template>

<script>
export default {
  name: "LessonListComponent",
  props: ['lessons', 'show_cancel_btn']
}
</script>

<style scoped>

</style>