<template>
  <div>
    <MasterStudentLayout>
      <div>
        <EditProfileComponent/>
      </div>
    </MasterStudentLayout>
  </div>
</template>


<script>
import MasterStudentLayout from "../Layouts/MasterStudentLayout";
import EditProfileComponent from "../../../components/Student/Profile/EditProfileComponent";

export default {
  name: "EditProfile",
  components: {EditProfileComponent, MasterStudentLayout},

}
</script>
