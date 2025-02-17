import {BaseApiService} from "../Base/BaseApiService";

export class FrontApiService extends BaseApiService {
    constructor() {
        super('drivisa');
    }

    async getNearestInstructors(location) {
        try {
            let params = new URLSearchParams();
            if (location != null) {
                params.append("latitude", location.coords.latitude);
                params.append("longitude", location.coords.longitude);
            }
            return await this.axios.get(this.getUrl('get-nearest-instructors?' + params));
        } catch (e) {
            this.handleErrors(e, "No Nearest Instructor Found!")
        }
    }


    async getCarMaker() {
        try {
            return await this.axios.get(this.getUrl('get-car-maker'));
        } catch (e) {
            this.handleErrors(e, "No Car Found!")
        }
    }


    async searchInstructors(pageNo, filter, isForward, isFilter, redirectRoute = true) {
        try {
            if (isForward) {
                pageNo = pageNo === 0 ? 1 : pageNo + 1;
            } else {
                pageNo = pageNo === 1 || pageNo === 0 ? 1 : pageNo - 1;
            }

            if (isFilter) {
                pageNo = 1;
            }

            let queryObj = {
                per_page: 12,
                page: pageNo,
            };
            let urlQueryObj = {
                per_page: 12,
                page: pageNo,
            };

            if (filter.term) {
                queryObj['filter[term]'] = filter.term;
                queryObj['filter[address]'] = filter.term;
                urlQueryObj.term = filter.term;
                urlQueryObj.address = filter.term;
            }

            if (filter.date) {
                queryObj['filter[date]'] = filter.date;
                urlQueryObj.date = filter.date;
            }
            if (filter.make) {
                queryObj['filter[make]'] = filter.make;
                urlQueryObj.make = filter.make;
            }
            if (filter.language) {

                urlQueryObj.language = filter.language;
            }

            queryObj['only_verified'] = "true";

            let query = new URLSearchParams(queryObj);
            let url_query = new URLSearchParams(urlQueryObj);

            if (redirectRoute) {
                window.history.replaceState({}, "", "/instructors?" + url_query)
            }

            return await this.axios.get(this.getUrl('search-instructors?type=web&' + query));
        } catch (e) {

        }
    }

}