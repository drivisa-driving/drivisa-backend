<template>
  <div>
    <h3 class="text-h5 text-center py-3">Lesson Details</h3>
    <div class="m-3">
      <table class="table table-bordered">
        <tr>
          <td style="width: 170px">Lesson No.</td>
          <td>{{ lesson.no }}</td>
        </tr>
        <tr>
          <td>Lesson Type</td>
          <td>{{ getLessonType(lesson.lesson_type) }}</td>
        </tr>
        <tr>
          <td>Status</td>
          <td
            v-if="
              getCurrentDateTime() > lesson.startAt &&
              lesson.status_text === 'reserved'
            "
          >
            Expired
            <span v-if="lesson.is_refund_initiated != 0"> (Refunded)</span>
          </td>
          <td v-else>{{ lesson.status_text }}</td>
        </tr>
        <tr>
          <td>Pick Point</td>
          <td>{{ getAddress(lesson.pickupPoint) }}</td>
        </tr>
        <tr>
          <td>Drop Point</td>
          <td>{{ getAddress(lesson.dropoffPoint) }}</td>
        </tr>
        <tr>
          <td>Cost</td>
          <td>${{ lesson.cost }}</td>
        </tr>
        <tr>
          <td>Lesson Tax</td>
          <td>${{ lesson.tax }}</td>
        </tr>
        <tr>
          <td>Additional Km.</td>
          <td>${{ lesson.additionalCost }}</td>
        </tr>
        <tr>
          <td>Additional Tax</td>
          <td>${{ lesson.additionalTax }}</td>
        </tr>
        <tr>
          <td>Total Purchase Amount</td>
          <td>${{ lesson.purchase_amount }}</td>
        </tr>
        <tr>
          <td>Payment By</td>
          <td>{{ lesson.paymentBy }}</td>
        </tr>
      </table>

      <div
        v-if="
          lesson.status_text === 'completed' ||
          (lesson.startAt > getCurrentDateTime() &&
            lesson.status_text === 'reserved')
        "
      >
        <table class="table table-bordered">
          <tr>
            <td colspan="2"><strong>Instructor Earnings</strong></td>
          </tr>
          <tr>
            <td style="width: 170px">Instructor Lesson Cost</td>
            <td>${{ lesson.instructorLessonCost }}</td>
          </tr>
          <tr>
            <td>Additional Km</td>
            <td>${{ lesson.additionalCost }}</td>
          </tr>
          <tr>
            <td>Total Earning</td>
            <td>
              ${{
                totalEarning(lesson.instructorLessonCost, lesson.additionalKM)
              }}
            </td>
          </tr>
        </table>
      </div>

      <table
        class="table table-bordered"
        v-if="
          lesson.status_text === 'rescheduled' &&
          lesson.reschedule_lesson_fee > 0
        "
      >
        <tr>
          <td style="width: 170px">Reschedule Compensation</td>
          <td>${{ lesson.reschedule_lesson_fee }}</td>
        </tr>
      </table>

      <table class="table table-bordered" v-if="lesson.lesson_cancellation">
        <tr>
          <td colspan="2"><strong>Refund Details</strong></td>
        </tr>
        <tr>
          <td>Cancel By</td>
          <td>{{ lesson.lesson_cancellation.cancel_by }}</td>
        </tr>
        <tr>
          <td>Reason</td>
          <td>{{ lesson.lesson_cancellation.reason }}</td>
        </tr>
        <tr>
          <td style="width: 170px">Cancel At</td>
          <td>
            {{
              lesson.lesson_cancellation.cancel_at
                | moment("ddd, MMMM DD, YYYY hh:mm a")
            }}
          </td>
        </tr>
        <tr>
          <td>Instructor Fee</td>
          <td>
            ${{
              lesson.lesson_cancellation.instructor_fee > 0
                ? lesson.lesson_cancellation.instructor_fee
                : "0.00"
            }}
          </td>
        </tr>
        <tr>
          <td>Cancellation Fee</td>
          <td>
            ${{
              lesson.lesson_cancellation.cancellation_fee > 0
                ? lesson.lesson_cancellation.cancellation_fee
                : "0.00"
            }}
          </td>
        </tr>
        <tr>
          <td>Refund</td>
          <td v-if="lesson.lesson_cancellation.cancel_by === 'instructor'">
            Refund Credit: {{ lesson.duration }}
          </td>
          <td v-else>${{ lesson.lesson_cancellation.refund_amount }}</td>
        </tr>
        <tr
          v-if="
            lesson.lesson_cancellation.cancel_by === 'instructor' &&
            lesson.lesson_cancellation.refund_amount > 0
          "
        >
          <td>Amount</td>
          <td>${{ lesson.lesson_cancellation.refund_amount }}</td>
        </tr>
      </table>

      <table
        class="table table-bordered"
        v-if="
          lesson.rescheduleDetails && lesson.rescheduleDetails.last_lesson_id
        "
      >
        <tr>
          <td colspan="2"><strong>Last Lesson Details</strong></td>
        </tr>
        <tr>
          <td style="width: 170px">Lesson No.</td>
          <td>{{ lesson.rescheduleDetails.last_lesson_no }}</td>
        </tr>
        <tr>
          <td style="width: 170px">Lesson Type</td>
          <td>{{ getLessonType(lesson.lesson_type) }}</td>
        </tr>
        <tr>
          <td>Start At</td>
          <td>{{ lesson.rescheduleDetails.last_startAt_formatted }}</td>
        </tr>
        <tr>
          <td>End At</td>
          <td>{{ lesson.rescheduleDetails.last_endAt_formatted }}</td>
        </tr>
        <tr>
          <td>Status</td>
          <td>{{ lesson.rescheduleDetails.last_status_text }}</td>
        </tr>
      </table>

      <table
        class="table table-bordered"
        v-if="
          lesson.rescheduleDetails && lesson.rescheduleDetails.new_lesson_id
        "
      >
        <tr>
          <td colspan="2"><strong>New Lesson Details</strong></td>
        </tr>
        <tr>
          <td style="width: 170px">Lesson No.</td>
          <td>{{ lesson.rescheduleDetails.new_lesson_no }}</td>
        </tr>
        <tr>
          <td style="width: 170px">Lesson Type</td>
          <td>{{ getLessonType(lesson.lesson_type) }}</td>
        </tr>
        <tr>
          <td>Start At</td>
          <td>{{ lesson.rescheduleDetails.new_startAt_formatted }}</td>
        </tr>
        <tr>
          <td>End At</td>
          <td>{{ lesson.rescheduleDetails.new_endAt_formatted }}</td>
        </tr>
        <tr>
          <td>Status</td>
          <td>{{ lesson.rescheduleDetails.new_status_text }}</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: "LessonDetailsDialog",
  props: ["lesson"],
  methods: {
    getCurrentDateTime() {
      let tzoffset = new Date().getTimezoneOffset() * 60000;
      let dateTime = new Date(Date.now() - tzoffset)
        .toISOString()
        .slice(0, 19)
        .replace("T", " ");

      return dateTime;
    },
    getAddress(data) {
      if (data) {
        return data.address;
      }
    },
    totalEarning(instructorLessonCost, additionalKM) {
      return (instructorLessonCost + additionalKM).toFixed(2);
    },
    getLessonType(lessonType) {
      if (lessonType === "Driving") {
        return "In Car Private Lesson";
      } else if (lessonType === "Bde") {
        return "BDE";
      } else {
        return lessonType;
      }
    },
  },
};
</script>