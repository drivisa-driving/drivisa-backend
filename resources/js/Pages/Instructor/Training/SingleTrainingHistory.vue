<template>
  <div>
    <MasterInstructorLayout>
      <v-dialog
            v-model="cancelDialog"
            width="500"
        >
          <v-card>
            <v-card-title class="text-h5 grey lighten-2">
              Reason
            </v-card-title>
            <v-textarea
                v-model="cancel.reason"
                placeholder="Enter Your Reason to Trainee"
                class="m-2"
                solo
                name="input-7-4"
            ></v-textarea>
            <v-divider></v-divider>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                  color="primary"
                  text
                  @click="cancelDialog = false"
              >
                No
              </v-btn>
              <v-btn
                  color="red accent-5"
                  text
                  @click="cancelTraining"
              >
                Yes
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

      <v-card elevation="0" class="border">
        <v-card-text>
          <div class="lesson" v-if="lesson != null">
            <div class="go-back">
              <div @click="$router.go(-1)" class="d-flex">
                <i class="mdi mdi-arrow-left mt-1 mr-2"></i>
                <h2 class="go-back-title">Training History</h2>
              </div>
            </div>

            <div class="d-flex flex-column traniee-info border">
              <div
                  class="d-flex flex-column flex-md-row justify-content-between"
              >
                <div class="d-flex">
                  <router-link
                      :to="{
                      name: 'instructor-trainee-profile-page',
                      params: { trainee_id: lesson.trainee.username },
                    }"
                  >
                    <img :src="lesson.trainee.avatar" alt="" class="avatar"/>
                  </router-link>
                  <div class="d-flex flex-column align-items-center">
                    <span class="name">{{ lesson.trainee.fullName }} </span>
                    <v-chip class="status">{{ lesson.status | status }}</v-chip>
                  </div>
                </div>

                <div class="d-flex flex-column">
                  <div class="d-flex justify-content-start align-items-center">
                    <i
                        class="mdi mdi-clock"
                        style="font-size: 18px; padding-left: 5px"
                    ></i>
                    &nbsp;
                    <span> {{ lesson.startAt | date }}</span>
                    <span
                    >&nbsp;({{ lesson.startAt | dateTime(false) }} -
                      {{ lesson.endAt | dateTime(false) }})</span
                    >
                  </div>
                  <div class="d-flex justify-content-start align-items-center">
                    &nbsp;
                    <i
                        class="mdi mdi-map-marker red--text"
                        style="font-size: 20px"
                    ></i>

                    <span> {{ lesson.pickupPoint.address }}</span>
                  </div>
                  <div
                      class="d-flex justify-content-start align-items-center pt-0"
                      v-if="lesson.dropoffPoint.address !== null"
                  >
                    &nbsp;
                    <i
                        class="mdi mdi-map-marker blue--text"
                        style="font-size: 20px"
                    ></i>
                    <span> {{ lesson.dropoffPoint.address }}</span>
                  </div>
                  <div class="">
                    <div class="px-0 py-1">
                      <v-btn
                          class="mb-1"
                          color="primary"
                          :disabled="
                          lesson.status == 1 ||
                          lesson.status == 3 ||
                          lesson.status == 4
                        "
                          block
                      >Notify Trainee
                      </v-btn>
                    </div>
                    <div class="px-0 py-1">
                      <v-btn
                          class="mb-1"
                          color="primary"
                          block
                          @click="trainingStartAt"
                          :disabled="
                          lesson.status == 2 ||
                          lesson.status == 3 ||
                          lesson.status == 4
                        "
                      >Start Lesson
                      </v-btn>
                    </div>
                    <div class="px-0 py-1">
                      <v-btn
                          class="mb-1"
                          color="primary"
                          block
                          @click="trainingEnd"
                          :disabled="
                          lesson.status == 1 ||
                          lesson.status == 3 ||
                          lesson.status == 4
                        "
                      >End Lesson
                      </v-btn>
                    </div>
                    <div class="px-0 py-1">
                      <v-btn
                          color="primary"
                          @click="cancelDialog = true"
                          :disabled="lesson.status == 4"
                          block
                      >Cancel Lesson
                      </v-btn>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="border lesson" v-if="lesson.status === 3"></div>

            <v-card-text>
              <v-tabs>
                <v-tab :hidden="lesson.lesson_type === 'Bde'">
                  Evaluations
                </v-tab
                >
                <v-tab :disabled="lesson.lesson_bde_number == null"> BDE Log</v-tab>
                <v-tab :disabled="lesson.lesson_bde_number == null"> Marking</v-tab>
                <v-tab :disabled="lesson.lesson_bde_number !== 10">
                  Final In-Car Test
                </v-tab
                >

                <v-tab-item class="mt-5">
                  <v-textarea
                      v-model="result.instructor_note"
                      :disabled="lesson.instructorNote != null"
                      outlined
                      auto-grow
                      placeholder="Ex. It makes me feel..."
                      label="Instructor Note"
                  ></v-textarea>
                  <div v-if="lesson.traineeNote">
                    <p>
                      <strong>Trainee Note:</strong> {{ lesson.traineeNote }}
                    </p>
                  </div>
                  <p v-if="lesson.instructorNote">
                    <strong>Instructor Note:</strong>
                    {{ lesson.instructorNote }}
                  </p>

                  <v-col
                      cols="12"
                      v-for="evaluation in evaluations"
                      :key="evaluation.id"
                  >
                    <v-subheader class="pl-0 mb-3">
                      {{ evaluation.title }}
                    </v-subheader>
                    <v-slider
                        :value="getResultPoint(evaluation.id)"
                        @input="updateResultPoint($event, evaluation.id)"
                        :max="evaluation.points == null ? 10 : evaluation.points"
                        step="1"
                        ticks="always"
                        :tick-size="5"
                        :thumb-size="20"
                        thumb-label="always"
                        :readonly="lesson.instructorEvaluations.length > 0"
                    ></v-slider>
                  </v-col>
                  <v-btn
                      color="primary"
                      @click="updateEval"
                      :disabled="lesson.instructorEvaluations.length > 0"
                  >
                    Save
                  </v-btn>
                </v-tab-item>
                <v-tab-item class="mt-5" v-if="lesson.lesson_bde_number">
                  <div class="mb-3">
                    <span>
                      <strong>
                        BDE Current Hour Number :
                        {{ lesson.lesson_bde_number }}
                      </strong>
                    </span>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <v-text-field
                          label="Date"
                          persistent-hint
                          outlined
                          prepend-inner-icon="mdi-calendar"
                      ></v-text-field>
                    </div>
                    <div class="col-md-6">
                      <v-text-field label="Time In" outlined></v-text-field>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <v-text-field label="Time Out" outlined></v-text-field>
                    </div>
                    <div class="col-md-6">
                      <v-text-field
                          label="Driving Instructor License Number"
                          outlined
                      ></v-text-field>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div
                          style="border: 1px solid #9e9e9e; border-radius: 5px"
                          class="p-2"
                      >
                        <label
                            for="instructor_signature"
                            style="font-size: 16px"
                        >Instructor Signature</label
                        >
                        <VueSignaturePad
                            style="width: 100%; height: 100px"
                            ref="signaturePad"
                            id="instructor_signature"
                        />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div
                          style="border: 1px solid #9e9e9e; border-radius: 5px"
                          class="p-2"
                      >
                        <label for="student_signature" style="font-size: 16px"
                        >Student Signature</label
                        >
                        <VueSignaturePad
                            style="width: 100%; height: 100px"
                            ref="signaturePad"
                            id="student_signature"
                        />
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <v-textarea outlined auto-grow label="Notes"></v-textarea>
                    </div>
                  </div>
                  <v-btn color="primary">Save</v-btn>
                </v-tab-item>
                <v-tab-item class="mt-5">
                  <div>
                    <span>Marking Key:</span>
                    <div class="row">
                      <div class="col-md-3">
                        <span><strong>E </strong>Exceeds Lesson Objective</span>
                      </div>
                      <div class="col-md-3">
                        <span><strong>M </strong>Meets Lesson Objective</span>
                      </div>
                      <div class="col-md-3">
                        <span><strong>P </strong>Progressing well</span>
                      </div>
                      <div class="col-md-3">
                        <span><strong>N </strong>Needs More Practice</span>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          label="Circle Check"
                          outlined
                      ></v-select>
                    </div>
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          outlined
                          label="Dash/Start/Controls"
                      ></v-select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          label="Moving/Braking"
                          outlined
                      ></v-select>
                    </div>
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          outlined
                          label="Position In Line "
                      ></v-select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          label="Hand Over Hand Steering Control"
                          outlined
                      ></v-select>
                    </div>
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          outlined
                          label="Turn Recovery"
                      ></v-select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          label="3 Stopping Positions"
                          outlined
                      ></v-select>
                    </div>
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          outlined
                          label="Signals (early/late)"
                      ></v-select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          label="Scanning (L.C.L.R)"
                          outlined
                      ></v-select>
                    </div>
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          outlined
                          label="Visual Skills 20-30 Sec"
                      ></v-select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          label="Right/Left Turns"
                          outlined
                      ></v-select>
                    </div>
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          outlined
                          label="Proper Speed Conditions"
                      ></v-select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          label="Following Distance"
                          outlined
                      ></v-select>
                    </div>
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          outlined
                          label="Staggering Vehicles"
                      ></v-select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          label="Yield Signs / Merge "
                          outlined
                      ></v-select>
                    </div>
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          outlined
                          label="Lane Changes"
                      ></v-select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          label="Backing & Front in Parking"
                          outlined
                      ></v-select>
                    </div>
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          outlined
                          label="Uphill & Downhill Parking"
                      ></v-select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          label="Parallel Parking"
                          outlined
                      ></v-select>
                    </div>
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          outlined
                          label="Roadside Stop"
                      ></v-select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          label="2 & 3 Point Turns"
                          outlined
                      ></v-select>
                    </div>
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          outlined
                          label="Rail Road Crossings"
                      ></v-select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          label="U Turns/Roundabout"
                          outlined
                      ></v-select>
                    </div>
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          outlined
                          label="One Way Streets"
                      ></v-select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          label="City Driving"
                          outlined
                      ></v-select>
                    </div>
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          outlined
                          label="Proper Use of See-Think-Do"
                      ></v-select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          label="80Km Highways"
                          outlined
                      ></v-select>
                    </div>
                    <div class="col-md-6">
                      <v-select
                          :items="marks"
                          outlined
                          label="100km Highways"
                      ></v-select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <v-select
                          :items="pass_y_n"
                          outlined
                          label="Pass/Fail [Y/N]"
                      ></v-select>
                    </div>
                  </div>
                  <v-btn color="primary">Save</v-btn>
                </v-tab-item>
                <v-tab-item class="mt-5">
                  <div class="p-1 border">
                    <div class="d-flex flex-wrap py-3 pl-3">
                      <h6>
                        Minor (x) = 2 &nbsp; &nbsp; Major (√) = 4 &nbsp; &nbsp;
                      </h6>
                      <h6>80% Minimum &nbsp; &nbsp; Y/N</h6>
                    </div>
                    <div class="border p-2 pt-3">
                      <h5>1. Start</h5>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width=75%;">
                          <span style="word-wrap: break-word">
                            unable to locate safety devices
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            fails to observe-uses mirrors only
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            incorrect use of
                            clutch/brake/accelerator/gears/steering
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            fails to signal when leaving
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                    </div>
                    <div class="border p-2 pt-3">
                      <h5>2. Backing</h5>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width=75%;">
                          <span style="word-wrap: break-word">
                            fails to look to rear before/while backing mirror
                            only
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            incorrect use of
                            clutch/brake/accelerator/gears/steering
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            turnabout: control/steering/Method/Observation
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                    </div>
                    <div class="border p-2 pt-3">
                      <h5>3. Driving Along</h5>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width=75%;">
                          <span style="word-wrap: break-word">
                            follows or passes too closely/cuts in too soon
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            speed: too fast/too slow for conditions
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            improper choice of lane/straddles lanes/unmarked
                            roadway
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            fails to check blind spot/observe properly
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            lane change signal wrong/early/not given/not
                            cancelled
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            right of way observance, ped./self/other vehicles
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            fails to use caution/obey
                            pedestrians/crossovers/school crossing
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            emergency vehicles/rail road crossings
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            incorrect use of
                            brake/accelerator/gears/steering/safety devices
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                    </div>
                    <div class="border p-2 pt-3">
                      <h5>4. Stop Park and Start On A Grade</h5>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width=75%;">
                          <span style="word-wrap: break-word">
                            rolls back when parking or starting
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            fails to angle wheels properly/fails to observe-use
                            mirrors
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            fails to set parking brake/fails to signal
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            incorrect use of (clutch/brake/accelerator)
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            gears/steering
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                    </div>
                    <div class="border p-2 pt-3">
                      <h5>5. Intersections / R.R. Crossings</h5>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width=75%;">
                          <span style="word-wrap: break-word">
                            fails to observe properly/controlled/uncontrolled
                            intersections
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            fails to obey signs or signals/pavement markings
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            late in slowing/slows too soon
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            stopping position: too soon or blocks crosswalk
                            intersections
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            right of ways observance ped./self/other vehicles
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                    </div>
                    <div class="border p-2 pt-3">
                      <h5>6. Turns</h5>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width=75%;">
                          <span style="word-wrap: break-word">
                            Signaling/wrong/early/late/not given/cancelled
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            fails to get in proper position/lane/late into lane
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            fails to check blind spot/observe properly
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            right of way observance
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            ped/self/other vehicles/position
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            turns too wide/cuts corner/enters wrong lane
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            steering: method/control/recovery
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            speed: too fast/too slow for
                            conditions/enter/leave/impedes
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            incorrect use of: clutch/brake/accelerator/gears
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                    </div>
                    <div class="border p-2 pt-3">
                      <h5>7. Parking</h5>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            fails to observe-uses mirrors only/backing and
                            leaving
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            hits: objects /other vehicles or climbs curbs
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            incorrect vehicle position
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            fails to signal/incorrect signal/leaving
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div
                          class="
                          d-flex
                          justify-space-between
                          flex-wrap
                          align-center
                          px-2
                        "
                      >
                        <div style="width: 75%">
                          <span style="word-wrap: break-word">
                            incorrect use of
                            clutch/brake/accelerator/gears/steering
                          </span>
                        </div>
                        <div class="d-flex">
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                          <v-checkbox></v-checkbox>
                        </div>
                      </div>
                      <div class="mt-3">
                        <div class="row">
                          <div class="col-md-6">
                            <v-text-field
                                label="Instructor Name"
                                outlined
                            ></v-text-field>
                          </div>
                          <div class="col-md-6">
                            <v-text-field
                                label="Driving Instructor License Number"
                                outlined
                            ></v-text-field>
                          </div>
                          <div class="col-md-12">
                            <v-menu
                                ref="menu"
                                v-model="menu"
                                :close-on-content-click="false"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                              <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    label="Date"
                                    prepend-inner-icon="mdi-calendar"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                    outlined
                                ></v-text-field>
                              </template>
                              <v-date-picker no-title scrollable>
                                <v-spacer></v-spacer>
                                <v-btn
                                    text
                                    color="primary"
                                    @click="$refs.menu.save(date)"
                                >
                                  OK
                                </v-btn>
                              </v-date-picker>
                            </v-menu>
                          </div>
                          <div class="col-md-12">
                            <div
                                style="
                                border: 1px solid #9e9e9e;
                                border-radius: 5px;
                              "
                                class="p-2"
                            >
                              <label
                                  for="instructor_signature"
                                  style="font-size: 16px"
                              >Instructor Signature</label
                              >
                              <VueSignaturePad
                                  style="width: 100%; height: 80px"
                                  ref="signaturePad"
                                  id="instructor_signature"
                              />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <v-btn color="primary m-3">Save</v-btn>
                  </div>
                </v-tab-item>
              </v-tabs>
            </v-card-text>
          </div>
        </v-card-text>
      </v-card>
    </MasterInstructorLayout>
  </div>
</template>

<script>
import MasterInstructorLayout from "../Layouts/MasterInstructorLayout";

export default {
  name: "SingleTrainingHistory",
  components: {MasterInstructorLayout},
  data() {
    return {
      lesson: null,
      evaluations: [],
      result: {
        instructor_note: null,
        instructor_evaluation: [],
      },
      marks: ["E", "M", "P", "N"],
      pass_y_n: ["Y", "N"],
      menu: false,
      cancelDialog: false,
      cancel: {
        reason: ""
      }
    };
  },
  mounted() {
    this.getLesson();
  },
  methods: {
    async getLesson() {
      try {
        let id = this.$route.params.id;
        const url = "/v1/drivisa/instructors/lessons/" + id;
        const {data} = await axios.get(url);
        this.lesson = data.data;
        this.result.instructor_note = this.lesson.instructorNote;
        if (this.lesson.status === 3) {
          await this.getEvaluations();
          this.updatePointIfEvaluationDone();
        }
      } catch (e) {
      }
    },
    async trainingStartAt() {
      try {
        let id = this.$route.params.id;
        const url = `/v1/drivisa/instructors/lessons/${id}/update-started-at`;
        const {data} = await axios.post(url);
        await this.getLesson();
        this.$toasted.success(data.message);
      } catch (e) {
        this.$root.handleErrorToast(e, "Enable to lesson start");
      }
    },
    async trainingEnd() {
      try {
        let id = this.$route.params.id;
        const url = `/v1/drivisa/instructors/lessons/${id}/update-ended-at`;
        const {data} = await axios.post(url);
        await this.getLesson();
        this.$toasted.success(data.message);
      } catch (e) {
      }
    },
    async cancelTraining() {
      try {
        let id = this.$route.params.id;
        const url = `/v1/drivisa/instructors/lessons/${id}/cancel-by-instructor`;
        const {data} = await axios.post(url, {
          reason: this.cancel.reason
        });
        this.cancelDialog = false;
        await this.getLesson();
        this.$toasted.success(data.message);
      } catch (e) {
      }
    },
    updatePointIfEvaluationDone() {
      if (this.lesson.instructorEvaluations.length > 0) {
        this.lesson.instructorEvaluations.forEach((Ieval) => {
          this.result.instructor_evaluation.find(
              (el) => el.id == Ieval.id
          ).value = Ieval.value;
        });
      }
    },
    async getEvaluations() {
      try {
        const url = "/v1/drivisa/instructors/evaluations";
        const {data} = await axios.get(url);
        this.evaluations = data.data;

        this.evaluations.forEach((elv) => {
          this.result.instructor_evaluation.push({
            id: elv.id,
            value: 0,
          });
        });
      } catch (e) {
      }
    },
    getResultPoint(id) {
      let elv = this.result.instructor_evaluation.find((el) => el.id == id);
      return elv.value;
    },
    updateResultPoint(value, id) {
      let elv = this.result.instructor_evaluation.find((el) => el.id == id);
      elv.value = value;
    },
    async updateEval() {
      try {
        let id = this.$route.params.id;
        const url = `/v1/drivisa/instructors/lessons/${id}/update-evaluation`;
        await axios.post(url, this.result);
        await this.getLesson();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to update Evaluation");
      }
    },

    undo() {
      this.$refs.signaturePad.undoSignature();
    },
    save() {
      const {isEmpty, data} = this.$refs.signaturePad.saveSignature();
      console.log(isEmpty);
      console.log(data);
    },
  },
};
</script>

<style scoped lang="scss">
.lesson {
  padding: 1rem;

  .go-back {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
    cursor: pointer;
  }

  .avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
  }

  .go-back-title {
    margin: 0;
    font: 500 20px/32px Montserrat, sans-serif;
    color: rgba(0, 0, 0, 0.87);
  }

  .border {
    border: 1px solid rgb(240, 240, 240);
    margin-top: -1px;

    &:first-child {
      border-top-left-radius: 5px;
      border-top-right-radius: 5px;
    }

    &:last-child {
      border-bottom-left-radius: 5px;
      border-bottom-right-radius: 5px;
    }

    .full-size-button {
      button {
        width: 100%;
        margin-top: 3px;
      }
    }
  }

  .traniee-info {
    div {
      padding-top: 1rem;
      padding-bottom: 1rem;
      padding-left: 0.5rem;
      padding-right: 0.5rem;
    }
  }

  .w-100 {
    width: 100%;
  }
}
</style>
