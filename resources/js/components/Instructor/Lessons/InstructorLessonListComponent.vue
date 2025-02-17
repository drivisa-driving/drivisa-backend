<template>
  <div>
    <v-simple-table v-if="lessons.length > 0">
      <template v-slot:default>
        <tbody>
        <tr
            v-for="lesson in lessons"
            :key="lesson.id"
        >
          <td class="p-2">
            <img :src="lesson.trainee.avatar" width="70" height="70" class="v-avatar" alt="">
            {{ lesson.trainee.fullName }}
          </td>
          <td>{{ lesson.startAt | dateTime }}</td>
          <td>{{ lesson.endAt | dateTime }}</td>
          <td>
            <v-chip>
              {{ lesson.status | status }}
            </v-chip>
          </td>
          <td>
            <router-link :to="{name:'instructor-single-training-history-page', params:{id:lesson.id}}">
              <span class="mdi mdi-dots-horizontal option-icon"></span>
            </router-link>
          </td>
        </tr>
        </tbody>
      </template>
    </v-simple-table>
    <div class="text-center mt-1" v-else>
      <h2 class="text-muted text-center">There are no lesson</h2>
    </div>
  </div>
</template>

<script>
export default {
  name: "InstructorLessonListComponent",
  props: ['lessons', 'show_cancel_btn']
}
</script>

<style scoped>

</style>