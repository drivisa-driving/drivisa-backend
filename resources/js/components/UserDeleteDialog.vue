<template>
  <div>
    <v-btn
        color="red accent-2"
        class="text-white text-capitalize"
        @click.stop="dialog = true"
    >
      Delete Account
    </v-btn>
    <v-dialog
        v-model="dialog"
        max-width="500px"
    >
      <v-card>
        <v-card-title class="text-h5">
          Delete account
        </v-card-title>

        <v-card-text>
          Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your
          account, please download any data or information that you wish to retain.
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn
              color="green darken-1"
              text
              @click="dialog = false"
          >
            Go Back
          </v-btn>

          <v-btn
              color="red accent-2"
              class="text-white text-capitalize"
              @click="$store.dispatch('deleteUser')"
          >
            Confirm
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>

</template>

<script>
export default {
  name: "UserDeleteDialog",
  data() {
    return {
      dialog: false
    }
  }
}
</script>

<style scoped>

</style>