<template>
  <div class="col-sm-6 col-md-4 col-lg-4">
    <div class="generic_content clearfix">
      <div class="generic_head_price clearfix">
        <div class="generic_head_content clearfix">
          <div class="head_bg"></div>
          <div class="head">
            <span class="text-white">{{ request.package.name }}</span>
          </div>
        </div>
      </div>
      <div class="p-3">
        <div class="card-items">
          <span style="font-size: 16px">
            <router-link
              class="text-decoration-underline"
              :to="'/admin/trainees-details/' + request.trainee.id"
            >
              {{ request.trainee.fullName }}
            </router-link>
          </span>
        </div>
        <div class="card-items">
          <i class="fas mdi mdi-calendar-blank-outline card-icon"></i>
          <span>
            {{ request.booking_date_formatted }},
            {{ request.booking_time_formatted }}
          </span>
        </div>
        <div class="card-items">
          <i class="fas mdi mdi-alarm card-icon"></i>
          <span>
            {{ request.time_duration }}
          </span>
        </div>
        <div class="card-items">
          <i class="fas mdi mdi-map-marker-radius card-icon"></i>
          <span>
            {{ request.location }}
          </span>
        </div>
        <div class="card-items">
          <i class="fas mdi mdi-map-marker-radius card-icon pick-point"></i>
          <span>
            {{ request.pickupPoint.address }}
          </span>
        </div>
        <div class="card-items">
          <i class="fas mdi mdi-map-marker-radius card-icon drop-point"></i>
          <span>
            {{ request.dropoffPoint.address }}
          </span>
        </div>
        <div class="card-items">
          <i class="fas mdi mdi-cash-multiple card-icon"></i>
          <span> ${{ request.package.package_data.instructor }} </span>
        </div>
        <div
          class="mt-2 mx-auto"
          style="font-size: 14px"
          v-if="request.is_reschedule_request !== 0"
        >
          <p><strong> ** This is reschedule request</strong></p>
        </div>
        <div
          v-if="request.status === 'accepted'"
          class="bottom-status not-paid-status"
        >
          Not Paid
        </div>
        <div
          v-else
          class="bottom-status"
          :class="{
            'paid-status': request.status === 'paid',
            'registered-status': request.status === 'registered',
            'rescheduled-status': request.status === 'rescheduled',
            'not-paid-status': request.status === 'declined',
          }"
        >
          {{ request.status }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["request"],
};
</script>

<style scoped>
.card-items {
  display: flex;
  justify-content: left !important;
  align-items: center !important;
  font-size: 13px;
  text-align: left !important;
  margin: 20px auto !important;
}
.card-icon {
  padding: 10px;
  font-size: 24px !important;
  background-color: #e1e9f8 !important;
  color: #3266cc !important;
  border-radius: 5px;
  margin-right: 5px;
}
i.pick-point {
  color: green !important;
}
i.drop-point {
  color: red !important;
}
.bottom-status {
  padding: 5px;
  border-radius: 5px;
}
.not-paid-status {
  background: #ffe5e6 !important;
  color: #d60605 !important;
}
.paid-status {
  background: #cefcd3 !important;
  color: #05a50f !important;
}
.registered-status {
  background: #3967d0 !important;
  color: #ffffff !important;
}
.rescheduled-status {
  background: black !important;
  color: #ffffff !important;
}
</style>