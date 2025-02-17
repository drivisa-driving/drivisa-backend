export default {
    methods: {
        $elvis: p => eval('this.' + p),
        handleErrorToast(e, custom_message) {
            if (e.response.status === 422) {
                if (e.response.data.errors) {
                    let errors = e.response.data.errors;
                    for (const key in errors) {
                        this.$toasted.error(errors[key][0])
                    }
                } else if (e.response.data.message) {
                    this.$toasted.error(e.response.data.message)
                } else {
                    this.$toasted.error("Validation Not Completed Successfully")
                }
            } else if (e.response.status === 500) {
                this.$toasted.error(e.response.data.message)
            } else if (e.response.status === 409) {
                if (e.response.data.message) {
                    this.$toasted.error(e.response.data.message)
                } else {

                }
            } else {
                this.$toasted.error(custom_message)
            }
        },
        async blockUser(user_id) {
            try {
                let url = '/v1/drivisa/admin/block-user';
                await axios.post(url, {
                    user_id: user_id
                })
                this.$toasted.success("User Blocked");
            } catch (e) {
                this.handleErrorToast(e, "Unable to Block User");
            }
        },
        async unblockUser(user_id) {
            try {
                let url = '/v1/drivisa/admin/unblock-user';
                await axios.post(url, {
                    user_id: user_id
                })
                this.$toasted.success("User Unblocked");
            } catch (e) {
                this.handleErrorToast(e, "Unable to Unblock User");
            }
        },
        isDateWithin24(dateTime) {
            const bookedLessonTime = new Date(dateTime);
            const now = new Date();

            const msBetweenDates = bookedLessonTime.getTime() - now.getTime();
            const hoursBetweenDates = msBetweenDates / (60 * 60 * 1000);

            return hoursBetweenDates < 24;

        }
    }
}
