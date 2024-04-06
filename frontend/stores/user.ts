import {createGlobalState, useAsyncState} from "@vueuse/core";
import {computed, ComputedRef, Ref} from "vue";
import {useInjectAxios} from "~/vendor/api.ts";
import {includes} from "lodash";
import {Permissions} from "~/stores/permissions.ts";

interface User {
    id: string,
    name: string,
    email: string,
    avatar?: string | null,
    permissions: Array<string>
}

interface UseUserStore {
    (): {
        isLoading: Ref<boolean>,
        isLoggedIn: ComputedRef<boolean>,
        user: ComputedRef<User | null>,
        isAdmin: ComputedRef<boolean>
    }
}

export const useUserStore: UseUserStore = createGlobalState(
    () => {
        const axios = useInjectAxios();
        const {
            isLoading,
            state
        } = useAsyncState(
            () => axios.get('/api/users/me').then(r => r.data),
            {
                isLoggedIn: false,
                user: null
            }
        );

        const isLoggedIn: ComputedRef<boolean> = computed(() => state.value.isLoggedIn);
        const user: ComputedRef<User | null> = computed(() => state.value.user);

        const isAdmin: ComputedRef<boolean> = computed(() => {
            if (isLoading.value || !isLoggedIn.value) {
                return false;
            }

            return includes(
                user.value.permissions,
                Permissions.All
            );
        });

        return {
            isLoading,
            isLoggedIn,
            user,
            isAdmin
        };
    }
)
