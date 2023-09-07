<template>
    <h1 v-if="isEditMode">
        Edit Label
    </h1>
    <h1 v-else>
        Create New Label
    </h1>

    <form @submit.prevent="submit">
        <div class="row g-2 mb-3">
            <form-group-field id="form_edit_name" class="col-md-6"
                              :field="v$.name"
                              label="Label Name"></form-group-field>
        </div>
        <div class="buttons">
            <button type="submit" class="btn btn-lg btn-primary">Save Changes</button>
        </div>
    </form>
</template>

<script setup lang="ts">
import {useRoute} from "vue-router";
import {computed, onMounted, ref} from "vue";
import mergeExisting from "~/functions/mergeExisting";
import {useNotify} from "~/functions/useNotify";
import {useVuelidateOnForm} from "~/functions/useVuelidateOnForm";
import {useInjectAxiosAuthenticated} from "~/api.ts";
import FormGroupField from "~/components/Form/FormGroupField.vue";

const loading = ref<boolean>(false);

const {params} = useRoute();

const isEditMode = computed(() => {
    return 'label_id' in params;
});

const apiUrl = computed(() => {
    return (isEditMode.value)
        ? `/api/label/${params.label_id}`
        : `/api/labels`;
});

const {
    form,
    v$,
    ifValid
} = useVuelidateOnForm(
    {},
    {}
);

const axios = useInjectAxiosAuthenticated();

onMounted(() => {
    if (isEditMode.value) {
        loading.value = true;

        axios.get(apiUrl.value).then((resp) => {
            form.value = mergeExisting(form.value, resp.data);
        }).finally(() => {
            loading.value = false;
        });
    }
});

const {notifySuccess} = useNotify();

const submit = () => {
    ifValid(() => {
        axios.request({
            method: (isEditMode.value)
                ? 'PUT'
                : 'POST',
            url: apiUrl.value,
            data: form.value
        }).then(() => {
            notifySuccess();
        });
    });
};
</script>
