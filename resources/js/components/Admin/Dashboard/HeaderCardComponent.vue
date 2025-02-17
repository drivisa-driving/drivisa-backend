<template>
  <v-container>
    <v-card>
      <div class="row pl-4 pr-4">
        <div class="col">
          <span class="col">
            <v-card class="mx-auto" max-width="344" outlined>
              <v-list-item three-line>
                <v-list-item-content>
                  <div class="text-overline mb-4 text-500 font-medium">
                    Instructors
                  </div>
                  <v-list-item-title class="text-h5 mb-1">
                    {{ stats.total_instructors }}
                  </v-list-item-title>
                  <v-list-item-subtitle>
                    {{ stats.total_instructors_in_day }} new in 24 hours
                  </v-list-item-subtitle>
                </v-list-item-content>

                <div class="tagset">
                  <span class="badge badge-grey">
                    <i
                      class="fas mdi mdi-account-details text-blue-500 text-xl"
                    ></i
                  ></span>
                </div>
              </v-list-item>
            </v-card>
          </span>
        </div>

        <div class="col">
          <span class="col">
            <v-card class="mx-auto" max-width="344" outlined>
              <v-list-item three-line>
                <v-list-item-content>
                  <div class="text-overline mb-4">Trainees</div>
                  <v-list-item-title class="text-h5 mb-1">
                    {{ stats.total_trainees }}
                  </v-list-item-title>
                  <v-list-item-subtitle>
                    {{ stats.total_trainees_in_day }} new in 24 hours
                  </v-list-item-subtitle>
                </v-list-item-content>

                <div class="tagset">
                  <span class="badge badge-grey">
                    <i class="fas mdi mdi-human-child text-blue-500 text-xl"></i
                  ></span>
                </div>
              </v-list-item>
            </v-card>
          </span>
        </div>

        <div class="col">
          <span class="col">
            <v-card class="mx-auto" max-width="344" outlined>
              <v-list-item three-line>
                <v-list-item-content>
                  <div class="text-overline mb-4">Verified Instructors</div>
                  <v-list-item-title class="text-h5 mb-1">
                    {{ stats.total_verified_instructors }}
                  </v-list-item-title>
                  <v-list-item-subtitle>
                    {{ stats.total_verified_instructors_in_day }} new in 24
                    hours
                  </v-list-item-subtitle>
                </v-list-item-content>

                <div class="tagset">
                  <span class="badge badge-grey">
                    <i
                      class="fas mdi mdi-car-connected text-blue-500 text-xl"
                    ></i
                  ></span>
                </div>
              </v-list-item>
            </v-card>
          </span>
        </div>

        <div class="col">
          <span class="col">
            <v-card class="mx-auto" max-width="344" outlined>
              <v-list-item three-line>
                <v-list-item-content>
                  <div class="text-overline mb-4">Verified Trainees</div>
                  <v-list-item-title class="text-h5 mb-1">
                    {{ stats.total_verified_trainees }}
                  </v-list-item-title>
                  <v-list-item-subtitle>
                    {{ stats.total_verified_trainees_in_day }} new in 24 hours
                  </v-list-item-subtitle>
                </v-list-item-content>

                <div class="tagset">
                  <span class="badge badge-grey">
                    <i class="fas pi pi-check-circle text-blue-500 text-xl"></i
                  ></span>
                </div>
              </v-list-item>
            </v-card>
          </span>
        </div>
      </div>
    </v-card>
  </v-container>
</template>

<script>
export default {
  name: "HeaderCardComponent",
  props: ["stats"],
};
</script>
<style scoped>
.badge {
  display: inline-block;
  margin: auto 10px;
  padding: 10px 30px !important;
}

.fas {
  padding: 0.2rem 0.3rem;
  position: absolute;
  /* You may need to change top and right. They depend on padding/widht of .badge */
  top: 10px;
  right: 10px;
  background: #b2d5f1;
  border-radius: 50%;
}
.mdi {
  font-size: 16px;
}
</style>
