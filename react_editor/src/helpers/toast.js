import { toast } from "react-toastify";

export const options = {
    type: toast.TYPE.SUCCESS,
    autoClose: 5000,
    hideProgressBar: true,
    position: toast.POSITION.BOTTOM_RIGHT,
    pauseOnHover: false,
    pauseOnFocusLoss: false,
    draggable: false,
    closeOnClick: false,
    className: "toast-background"
};
