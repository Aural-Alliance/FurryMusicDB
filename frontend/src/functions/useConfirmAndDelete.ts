import {useSweetAlert} from "~/vendor/sweetalert";
import {useNotify} from "~/functions/useNotify";
import {useInjectAxiosAuthenticated} from "~/vendor/api.ts";

export default function useConfirmAndDelete(
    confirmMessage,
    onSuccess = null
) {
    const {confirmDelete} = useSweetAlert();
    const {notifySuccess} = useNotify();
    const axios = useInjectAxiosAuthenticated();

    const doDelete = (deleteUrl) => {
        confirmDelete({
            title: confirmMessage
        }).then((result) => {
            if (result.value) {
                axios.delete(deleteUrl).then((resp) => {
                    notifySuccess(resp.data.message);

                    if (typeof onSuccess === 'function') {
                        onSuccess(resp.data);
                    }
                });
            }
        });
    };

    return {
        doDelete
    };
}
