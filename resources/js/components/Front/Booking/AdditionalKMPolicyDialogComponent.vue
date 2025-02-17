<template>
  <v-row>
    <v-dialog
        v-model="dialog"
        max-width="350"
    >
      <v-card>
        <v-card-title class="text-h5">
          Pick-up & Drop-off Point
        </v-card-title>

        <v-card-text>
          Additional $1/km will be applied if your pick-up and drop-off point is above 10 kms from Instructor's default
          location.
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
              color="green darken-1"
              text
              @click="dialog = false"
          >
            OK
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
export default {
  name: "AdditionalKMPolicyDialogComponent",
  data() {
    return {
      dialog: false,
    }
  },
  methods: {
    openDialog() {
      this.dialog = true;
    }
  }
}
</script>

<style scoped>

</style>