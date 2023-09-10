<template>
    <h1>{{ meta.title }}</h1>

    <loading :loading="isLoading">
        <form @submit.prevent="submit">
            <div class="row g-2 mb-3">
                <form-group-field id="form_edit_title" class="col-md-6"
                                  :field="v$.title"
                                  label="Track Title"></form-group-field>
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
import {required} from "@vuelidate/validators";
import Loading from "~/components/Common/Loading.vue";

const isLoading = ref<boolean>(false);

const {meta, params} = useRoute();
const isEditMode = 'track_id' in params;

const apiUrl = computed(() => {
    return (isEditMode)
        ? `/profile/artist/${params.artist_id}/album/${params.album_id}/track/${params.track_id}`
        : `/profile/artist/${params.artist_id}/album/${params.album_id}/tracks`;
});

const {
    form,
    v$,
    ifValid
} = useVuelidateOnForm(
    {
        title: {required}
    },
    {
        title: ''
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

            router.push({
                name: 'profile:artist:album:tracks',
                params: {
                    artist_id: params.artist_id,
                    album_id: params.album_id
                }
            });
        });
    });
};
</script>
