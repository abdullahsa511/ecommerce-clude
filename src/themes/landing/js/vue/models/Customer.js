export default class Customer {
    constructor(data) {
        this.uuid = data?.uuid??null;
        this.name = data?.name??null;
        this.email = data?.email??null;
        this.phone = data?.phone??null;
        this.otp = data?.otp??null;
        this.org_code = data?.org_code??null;
        this.customer_id = data?.customer_id??null;
        this.is_verified = data?.is_verified??false;

    }
}