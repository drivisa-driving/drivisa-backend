<template>
  <div>
    <h3 class="text-h5 text-center pt-2">Messages</h3>
    <div class="m-2">
      <div class="chat-header">
        <p>
          {{ data.startAt_date_formatted }}, {{ data.openAt_formatted }} to
          {{ data.closeAt_formatted }}
          <br />
          (
          <span v-if="data.lesson_type === 'Driving'">
            In Car Private Lesson
          </span>
          <span v-else-if="data.lesson_type === 'Bde'"> BDE </span>
          <span v-else> {{ data.lesson_type }} </span>
          )
        </p>
      </div>
      <section class="chat-area">
        <div v-for="(message, m) in messages" :key="m">
          <div class="mb-2">
            <p
              class="message mb-0"
              :class="{
                'message-out': message.message_by === 'trainee',
                'message-in': message.message_by !== 'trainee',
              }"
            >
              {{ message.text }}
            </p>
            <p
              :class="{
                'message-out-date': message.message_by === 'trainee',
                'message-in-date': message.message_by !== 'trainee',
              }"
            >
              {{ message.timestamp | moment("MMM. DD, YYYY h:mm A") }}
              <br />
              <span style="font-size: 12px; opacity: 0.5">
                ({{ message.message_by }})
              </span>
            </p>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
export default {
  name: "MessageDetails",
  props: ["data", "messages"],
};
</script>