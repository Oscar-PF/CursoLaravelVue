<template>
  <div class="card my-2">
    <div class="card-header">
      <h3>{{ title }}</h3>
    </div>

    <div class="card-body">
      <div v-for="(input, i) in target[prop]" :key="i" class="d-flex mb-4 p-0">
        <div class="col-10 p-0">
          <input
            type="text"
            :placeholder="placeholder"
            class="form-control"
            v-model="target[prop][i]"
          />
        </div>
        <div class="col-2">
          <div class="d-flex justify-content-end">
            <button class="btn btn-danger" @click="remove(i)">
              <i class="fa fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="col-2">
      <div class="d-flex mb-2 ms-3">
        <div class="btn btn-primary" @click="add()">Add</div>
      </div>
    </div>
  </div>
</template>

<script>
import mixin from './mixin';

export default {
	name: 'ListForm',

  props: {
    placeholder: {
      type: String,
      default: () => '',
    }
  },

	mixins: [mixin],

	data() {
		return {
			push: () => '',
		}
	},
};
</script>