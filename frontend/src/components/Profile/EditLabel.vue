<template>
    <h1>{{ meta.title }}</h1>

    <loading :loading="isLoading">
        <form @submit.prevent="submit">
            <div class="row g-2 mb-3">
                <form-group-field id="form_edit_name" class="col-md-12"
                                  :field="v$.name"
                                  label="Label Name"></form-group-field>

                <form-group-field input-type="textarea" id="form_edit_description" class="col-md-12"
                                  :field="v$.description"
                                  label="Description"></form-group-field>

                <form-group id="form_edit_avatar" class="col-md-12">
                    <template #label>Upload New Avatar</template>
                    <template #description>Leave this field blank to use the existing avatar.</template>

                    <form-file v-model="avatar" accept="image/*"></form-file>
                </form-group>
            </div>

            <fieldset>
                <legend>Social Links</legend>

                <div class="row g-2 mb-3">
                    <form-group-field input-type="url" id="form_edit_url" class="col-md-6"
                                      :field="v$.url"
                                      label="Web Site"></form-group-field>
                </div>
            </fieldset>

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
import FormFile from "~/components/Form/FormFile.vue";
import FormGroup from "~/components/Form/FormGroup.vue";
import getFileBase64 from "~/functions/getFileBase64";

const isLoading = ref<boolean>(false);

const avatar = ref<File | null>(null);

const {meta, params} = useRoute();
const isEditMode = 'label_id' in params;

const apiUrl = computed(() => {
    return (isEditMode)
        ? `/profile/label/${params.label_id}`
        : `/profile/labels`;
});

const {
    form,
    v$,
    validate
} = useVuelidateOnForm(
    {
        name: {
            required,
            maxLength: maxLength(255)
        },
        description: {},
        url: {
            maxLength: maxLength(255)
        }
    },
    {
        name: '',
        description: '',
        url: ''
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

const submit = async () => {
    await validate();

    const avatarStr = await getFileBase64(avatar.value);

    const formData = form.value;
    formData.avatar = avatarStr;

    await axios.request({
        method: (isEditMode)
            ? 'PUT'
            : 'POST',
        url: apiUrl.value,
        data: formData
    });

    notifySuccess();
    await router.push({name: 'profile'});
};
</script>
