<template>
    <div class="row gy-2 gx-3 align-items-start mb-3">
        <div class="col-auto">
            <form-group-select :id="'form_edit_social_'+index+'_type'" :options="typeOptions"
                               :field="v$.type"></form-group-select>
        </div>
        <div class="col-auto" v-if="row.type === 'custom'">
            <form-group-field :id="'form_edit_social_'+index+'_name'" class="col-md-12"
                              :field="v$.name" :input-attrs="{placeholder: 'Site Name'}"></form-group-field>
        </div>
        <div class="col-auto">
            <form-group-field :id="'form_edit_social_'+index+'_value'" class="col-md-12"
                              :field="v$.value"></form-group-field>
        </div>
        <div class="col-auto">
            <button
                type="button"
                class="btn btn-danger"
                @click="doRemove()"
            >
                <icon icon="dash-circle-fill"/>
                <span>
                    Remove
                </span>
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import Icon from "~/components/Common/Icon.vue";
import {required} from "@vuelidate/validators";
import {computed, toRef} from "vue";
import useVuelidate from "@vuelidate/core";
import FormGroupField from "~/components/Form/FormGroupField.vue";
import FormGroupSelect from "~/components/Form/FormGroupSelect.vue";
import {getSocialName, SocialType} from "~/entities/Socials.ts";

const props = defineProps({
    index: {
        type: Number,
        required: true
    },
    row: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['remove']);

const row = toRef(props, 'row');

const v$ = useVuelidate(
    computed(() => {
        let validations = {
            type: {required},
            name: {},
            value: {required}
        };

        if (row.value.type === 'custom') {
            validations.name = {required};
        }

        return validations;
    }),
    row
);

let typeOptions = [];
Object.values(SocialType).forEach((type) => {
    typeOptions.push({
        value: type,
        text: getSocialName(type)
    })
});

const doRemove = () => {
    emit('remove');
};
</script>
