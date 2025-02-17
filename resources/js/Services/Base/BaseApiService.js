import axios from "axios";

export class BaseApiService {
    baseUrl = "/api/v1/";
    resource;
    axios;

    constructor(resource) {
        if (!resource) throw new Error("Resource Not Provided");
        this.resource = resource;
        this.axios = axios.create({
            baseURL: this.baseUrl
        })
    }

    getUrl(id = "") {
        return `${this.resource}/${id}`;
    }


    handleErrors(err, custom_message) {
        console.log(err);
        if (err.response.status === 422) {
            let errors = err.response.data.errors;
            for (const key in errors) {
                this.$toasted.error(errors[key][0])
            }
        } else if (err.response.status === 500) {
            this.$toasted.error(err.response.data.message)
        } else {
            this.$toasted.error(custom_message)
        }
    }

}