<template>
    <h1>{{ meta.title }}</h1>

    <loading :loading="isLoading">
        <form @submit.prevent="submit">
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <form-group-field id="form_edit_name" class="mb-3"
                                      :field="v$.name"
                                      label="Artist Name"></form-group-field>

                    <form-group-field input-type="textarea" id="form_edit_description"
                                      :field="v$.description"
                                      label="Description"></form-group-field>
                </div>
                <div class="col-md-6">
                    <form-group id="form_edit_avatar">
                        <template #label>
                            <template v-if="isEditMode">
                                Upload New Avatar
                            </template>
                            <template v-else>
                                Upload Avatar
                            </template>
                        </template>
                        <template #description v-if="isEditMode">Leave this field blank to use the existing avatar.
                        </template>

                        <form-file v-model="avatar" accept="image/*"></form-file>
                    </form-group>
                </div>
            </div>

            <hr>

            <common-social-fields :socials="form.socials"/>

            <hr>

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
import {useInjectAxios} from "~/vendor/api";
import FormGroupField from "~/components/Form/FormGroupField.vue";
import {maxLength, required} from "@vuelidate/validators";
import Loading from "~/components/Common/Loading.vue";
import FormFile from "~/components/Form/FormFile.vue";
import FormGroup from "~/components/Form/FormGroup.vue";
import getFileBase64 from "~/functions/getFileBase64";
import CommonSocialFields from "~/components/Profile/CommonSocialFields.vue";

const isLoading = ref<boolean>(false);

const avatar = ref<File | null>(null);

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
    validate
} = useVuelidateOnForm(
    {
        name: {
            required,
            maxLength: maxLength(255)
        },
        description: {},
    },
    {
        name: '',
        description: '',
        socials: [],
    }
);

const axios = useInjectAxios();

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

    if ('label_id' in params) {
        await router.push({
            name: 'profile:label:artists',
            params: {
                label_id: params.label_id
            }
        });
    } else {
        await router.push({name: 'profile'});
    }
};
</script>
