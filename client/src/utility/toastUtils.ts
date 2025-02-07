import { useToast } from 'vue-toastification';


const toast = useToast();


export const showErrorToast = (error: any): void => {
  if (
    error.response &&
    error.response.data &&
    error.response.data.errors
  ) {
    const errors = error.response.data.errors;
    for (const key in errors) {
      if (Object.prototype.hasOwnProperty.call(errors, key)) {
        errors[key].forEach((errorMessage: string) => {
          toast.error(errorMessage);
        });
      }
    }
  } else if (
    error.response &&
    error.response.data &&
    error.response.data.message
  ) {
    toast.error(error.response.data.message);
  } else {
    toast.error('An unexpected error occurred. Please try again.');
  }
};


export const showSuccessToast = (message: string): void => {
  toast.success(message);
};


export const showInfoToast = (message: string): void => {
  toast.info(message);
};


export const showWarningToast = (message: string): void => {
  toast.warning(message);
};
