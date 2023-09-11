<template>
    <h1>Explore Labels</h1>

    <div class="row">
        <div class="col-md-4 col-lg-3 mb-md-0 mb-3">
            <h3>Filter Results</h3>

            <form @submit.prevent="relist">
                <div class="mb-3">
                    <form-group-field id="filter_search"
                                      label="Search Phrase"
                                      :field="v$.search"
                    />
                </div>

                <div class="block-buttons">
                    <button type="submit" class="btn btn-lg btn-primary">
                        Apply Filters
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" @click="clearFilter">
                        Reset All
                    </button>
                </div>
            </form>
        </div>

        <div class="col-md-8 col-lg-9">
            <h3>Labels ({{ state.total }})</h3>

            <loading :loading="isLoading">
                <pagination v-if="state.total_pages > 1" :per-page="perPage" :total="state.total_pages"
                            :current-page="currentPage" @change="relist"/>

                <div class="d-flex align-items-center mb-3" v-for="row in state.rows" :key="row.id">
                    <div class="flex-shrink-0">
                        <router-link :to="{name: 'label', params: {label_id: row.id}}"
                                     class="text-big fw-bold">
                            <avatar type="artist" :id="row.id" :art-updated-at="0"/>
                        </router-link>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="text-big fw-bold">
                            <router-link :to="{name: 'label', params: {label_id: row.id}}"
                                         class="text-big fw-bold">
                                {{ row.name }}
                            </router-link>

                            <span class="badge ms-2 text-small text-bg-secondary">One Day Ago</span>
                        </div>
                        <p class="mb-0">
                            Description description!
                            {{ row.description }}
                        </p>
                    </div>
                </div>
            </loading>
        </div>
    </div>
</template>

<script setup lang="ts">
import {useVuelidateOnForm} from "~/functions/useVuelidateOnForm.ts";
import FormGroupField from "~/components/Form/FormGroupField.vue";
import {useInjectAxiosPublic} from "~/vendor/api.ts";
import {useAsyncState} from "@vueuse/core";
import Loading from "~/components/Common/Loading.vue";
import {ref, toRaw} from "vue";
import Pagination from "~/components/Common/Pagination.vue";
import Avatar from "~/components/Common/Avatar.vue";

const {form, resetForm, v$} = useVuelidateOnForm(
    {
        search: {}
    },
    {
        search: ''
    }
);

const perPage = 15;
const currentPage = ref<number>(1);

const axios = useInjectAxiosPublic();
const {state, isLoading, execute: relist} = useAsyncState(
    () => {
        const filters = toRaw(form.value);
        const queryParams = {
            per_page: perPage,
            page: currentPage.value,
            ...filters
        };

        return axios.get(
            '/labels',
            {
                params: queryParams
            }
        ).then(r => r.data);
    },
    {
        total: 0,
        total_pages: 1,
        rows: []
    }
);

const clearFilter = () => {
    currentPage.value = 1;

    resetForm();
    relist();
}
</script>
