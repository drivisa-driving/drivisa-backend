<template>
  <div>
    <MasterInstructorLayout>
      <div>
        <EditProfileComponent/>
      </div>
    </MasterInstructorLayout>
  </div>
</template>


<script>
import MasterInstructorLayout from "../Layouts/MasterInstructorLayout";
import EditProfileComponent from "../../../components/Instructor/Profile/EditProfileComponent";

export default {
  name: "EditProfile",
  components: {
    MasterInstructorLayout,
    EditProfileComponent
  },

}
</script>
