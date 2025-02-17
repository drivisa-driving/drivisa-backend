<template>
  <v-data-table
      :headers="headers"
      :items="cars"
      class="elevation-1"
  >
    <template v-slot:top>
      <v-toolbar
          flat
      >
        <v-toolbar-title>Add Car</v-toolbar-title>
        <v-divider
            class="mx-4"
            inset
            vertical
        ></v-divider>
        <v-spacer></v-spacer>
        <v-dialog
            v-model="dialog"
            max-width="500px"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn
                color="primary"
                dark
                class="mb-2"
                v-bind="attrs"
                v-on="on"
            >
              Add New Car
            </v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="text-h5">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-row>
                  <v-col
                      cols="12"
                      sm="6"
                      md="4"
                  >
                    <v-text-field
                        v-model="editedItem.make"
                        label="Make"
                    ></v-text-field>
                  </v-col>
                  <v-col
                      cols="12"
                      sm="6"
                      md="4"
                  >
                    <v-text-field
                        v-model="editedItem.model"
                        label="Model"
                    ></v-text-field>
                  </v-col>
                  <v-col
                      cols="12"
                      sm="6"
                      md="4"
                  >
                    <VueDatePicker
                        style="margin-top: 17px;border-bottom: 1px solid black"
                        v-model="editedItem.generation"
                        min-date="1980"
                        no-calendar-icon
                        placeholder="YYYY"
                        type="year"
                    />
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                  color="blue darken-1"
                  text
                  @click="close"
              >
                Cancel
              </v-btn>
              <v-btn
                  color="blue darken-1"
                  text
                  @click="save"
              >
                Save
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialogDelete" max-width="500px">
          <v-card>
            <v-card-title class="text-h5">Are you sure you want to delete this item?</v-card-title>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
              <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
              <v-spacer></v-spacer>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
    </template>
    <template v-slot:item.actions="{ item }">
      <v-icon
          small
          color="yellow darken-1"
          class="mr-2"
          @click="editItem(item)"
      >
        mdi-pencil
      </v-icon>
      <v-icon
          small
          color="red"
          @click="deleteItem(item)"
      >
        mdi-delete
      </v-icon>
    </template>
    <template v-slot:no-data>
      <h5>No car available</h5>
    </template>
  </v-data-table>
</template>

<script>
import {VueDatePicker} from '@mathieustan/vue-datepicker';
import '@mathieustan/vue-datepicker/dist/vue-datepicker.min.css';

export default {
  components: {
    VueDatePicker
  },
  data() {
    return {
      dialog: false,
      dialogDelete: false,
      headers: [
        {text: 'Make', align: 'start', value: 'make',},
        {text: 'Model', value: 'model'},
        {text: 'Year', value: 'generation'},
        {text: 'Actions', value: 'actions', sortable: false},
      ],
      isNoCars: true,
      cars: [],
      editedIndex: -1,
      editedItem: {
        model: '',
        make: '',
        generation: null,
      },
      defaultItem: {
        model: '',
        make: '',
        generation: null,
      },
    }
  },

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'New Car' : 'Edit Item'
    },
  },

  watch: {
    dialog(val) {
      val || this.close()
    },
    dialogDelete(val) {
      val || this.closeDelete()
    },
  },

  created() {
    this.getCars()
  },

  methods: {
    async getCars() {
      try {
        const {data} = await axios.get('/v1/drivisa/instructors/cars');
        this.cars = data.data;

        this.isNoCars = this.cars.length === 0;
        this.$emit('carAdded', this.isNoCars);


      } catch (e) {

      }
    },

    editItem(item) {
      this.editedIndex = this.cars.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem(item) {
      this.editedIndex = this.cars.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialogDelete = true
    },

    async deleteItemConfirm() {
      try {
        let id = this.cars[this.editedIndex].id;
        await axios.delete('/v1/drivisa/instructors/cars/' + id);
        this.dialog = false;
        await this.getCars();
        this.$toasted.success("Car Deleted");
        this.cars.splice(this.editedIndex, 1)
      } catch (e) {
        this.$toasted.error("Unable to Delete Car");
      } finally {
        this.closeDelete()
      }


    },

    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },

    closeDelete() {
      this.dialogDelete = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },

    async save() {
      if (this.editedIndex > -1) {
        const url = "/v1/drivisa/instructors/cars/" + this.cars[this.editedIndex].id;
        await axios.post(url, this.editedItem);
        Object.assign(this.cars[this.editedIndex], this.editedItem);
        this.$toasted.success("Car was updated successfully");
      } else {
        await axios.post("/v1/drivisa/instructors/cars", this.editedItem);
        this.cars.push(this.editedItem);
        this.$toasted.success("Car was added successfully");
      }

      this.isNoCars = this.cars.length === 0;

      this.$emit('carAdded', this.isNoCars);
      this.close()
    },
  },
}
</script>
<style scoped>

</style>