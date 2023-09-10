<template>
    <h1>{{ meta.title }}</h1>

    <loading :loading="isLoading">
        <form @submit.prevent="submit">
            <div class="row g-2 mb-3">
                <form-group-field id="form_edit_name" class="col-md-12"
                                  :field="v$.name"
                                  label="Artist Name"></form-group-field>
            </div>
            <div class="buttons">
                <button type="submit" class="btn btn-lg btn-primary">Save Changes</button>
            </div>
        </form>
    </loading>
</template>

<script setup lang="ts">
import {useRoute, useRouter} from "vue-router";
import {computed, onMounted, ref} from "vue";
import mergeExisting from "~/functions/mergeExisting";
import {useNotify} from "~/functions/useNotify";
import {useVuelidateOnForm} from "~/functions/useVuelidateOnForm";
import {useInjectAxiosAuthenticated} from "~/vendor/api";
import FormGroupField from "~/components/Form/FormGroupField.vue";
import {maxLength, required} from "@vuelidate/validators";
import Loading from "~/components/Common/Loading.vue";

const isLoading = ref<boolean>(false);

const {meta, params} = useRoute();
const isEditMode = 'artist_id' in params;

const apiUrl = computed(() => {
    const prefix = ('label_id' in params)
        ? `/profile/label/${params.label_id}`
        : '/profile';

    return (isEditMode)
        ? `${prefix}/artist/${params.artist_id}`
        : `${prefix}/artists`;
});

const {
    form,
    v$,
    ifValid
} = useVuelidateOnForm(
    {
        name: {
            required,
            maxLength: maxLength(255)
        }
    },
    {
        name: ''
    }
);

const axios = useInjectAxiosAuthenticated();

onMounted(() => {
    if (isEditMode) {
        isLoading.value = true;

        axios.get(apiUrl.value).then((resp) => {
            form.value = mergeExisting(form.value, resp.data);
        }).finally(() => {
            isLoading.value = false;
        });
    }
});

const {notifySuccess} = useNotify();
const router = useRouter();

const submit = () => {
    ifValid(() => {
        axios.request({
            method: (isEditMode)
                ? 'PUT'
                : 'POST',
            url: apiUrl.value,
            data: form.value
        }).then(() => {
            notifySuccess();

            if ('label_id' in params) {
                router.push({
                    name: 'profile:label:artists',
                    params: {
                        label_id: params.label_id
                    }
                });
            } else {
                router.push({name: 'profile'});
            }
        });
    });
};
</script>
