<template>
    <loading :loading="isLoading">
        <form @submit.prevent="submit">
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <form-group-field id="form_edit_name" class="mb-3"
                                      :field="v$.name"
                                      :label="nameLabel"/>

                    <form-group-field input-type="textarea" id="form_edit_description"
                                      :field="v$.description"
                                      label="Description"/>
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

            <fieldset>
                <legend>Social Links</legend>

                <social-field
                    v-for="(row, index) in form.socials"
                    :key="index"
                    v-model:row="form.socials[index]"
                    :index="index"
                    @remove="removeSocial(index)"
                />

                <div class="buttons mb-3">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="addSocial"
                    >
                        <icon icon="plus-circle"/>
                        <span>
                            Add Social Link
                        </span>
                    </button>
                </div>
            </fieldset>

            <hr>

            <div class="buttons">
                <button type="submit" class="btn btn-lg btn-primary">Save Changes</button>
            </div>
        </form>
    </loading>
</template>

<script setup lang="ts">
import {maxLength, required} from "@vuelidate/validators";
import {useNotify} from "~/functions/useNotify.ts";
import {useRouter} from "vue-router";
import getFileBase64 from "~/functions/getFileBase64.ts";
import {useInjectAxios} from "~/vendor/api.ts";
import {computed, onMounted, ref} from "vue";
import mergeExisting from "~/functions/mergeExisting.ts";
import Loading from "~/components/Common/Loading.vue";
import FormGroupField from "~/components/Form/FormGroupField.vue";
import FormFile from "~/components/Form/FormFile.vue";
import FormGroup from "~/components/Form/FormGroup.vue";
import SocialField from "~/components/Profile/SocialField.vue";
import Icon from "~/components/Common/Icon.vue";
import {useVuelidateOnForm} from "~/functions/useVuelidateOnForm.ts";

const props = defineProps<{
    apiUrl: string,
    isEditMode: boolean,
    listingType: string
}>();

const emit = defineEmits(['submitted']);

const isLoading = ref<boolean>(false);

const avatar = ref<File | null>(null);

const nameLabel = computed(() => {
    return (props.listingType === 'label')
        ? 'Label Name'
        : 'Artist Name';
});

const addSocial = () => {
    form.value.socials.push({
        type: 'website',
        name: null,
        value: null
    });
};

const removeSocial = (index) => {
    form.value.socials.splice(index, 1);
};

const axios = useInjectAxios();

onMounted(() => {
    if (props.isEditMode) {
        isLoading.value = true;

        axios.get(props.apiUrl).then((resp) => {
            form.value = mergeExisting(form.value, resp.data);
        }).finally(() => {
            isLoading.value = false;
        });
    }
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
        socials: []
    }
);

const {notifySuccess} = useNotify();
const router = useRouter();

const submit = async () => {
    await validate();

    const avatarStr = await getFileBase64(avatar.value);

    const formData = form.value;
    formData.avatar = avatarStr;

    await axios.request({
        method: (props.isEditMode)
            ? 'PUT'
            : 'POST',
        url: props.apiUrl,
        data: formData
    });

    notifySuccess();

    emit('submitted');
};
</script>
