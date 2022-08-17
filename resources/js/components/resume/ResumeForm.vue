<template>
  <div>
    <Alert
      v-if="Array.isArray(alert.messages) && alert.messages.length > 0 || typeof alert.messages === 'string'" 
      :messages="alert.messages"
      :type="alert.type"
    />
    <div class="row mb-3">
      <div class="col-sm-8">
        <div class="form-group">
          <input 
            v-model="resume.title"
            placeholder="Resume Title"
            required
            autofocus
            class="form-control w-100"
            />
        </div>
      </div>

      <div class="col-sm-4">
        <button class="btn btn-success btn-block" @click="submit()">
          Submit <i class="fa fa-upload"></i>
        </button>
      </div>
    </div>

    <Tabs>
      <Tab title="Basics" icon="fas fa-user">
        <VueFormGenerator
          :schema="schemas.basics"
          :model="resume.content.basics"
          :options="options"
        />
        <VueFormGenerator
          :schema="schemas.location"
          :model="resume.content.basics.location"
          :options="options"
        />
      </Tab>
      <Tab title="Profiles" icon="fa fa-users">
        <DynamicForm
          title="Profile"
          self="profiles"
          :model="resume.content.basics"
          :schema="schemas.profiles"
          ></DynamicForm>
      </Tab>
      <Tab title="Work" icon="fa fa-briefcase">
        <DynamicForm
          title="Work"
          self="work"
          :model="resume.content"
          :schema="schemas.work"
          :subforms="subforms.work"
          ></DynamicForm>
      </Tab>
      <Tab title="Education" icon="fa fa-graduation-cap">
        <DynamicForm
          title="Education"
          self="education"
          :model="resume.content"
          :schema="schemas.education"
          :subforms="subforms.education"
          ></DynamicForm>
      </Tab>
      <Tab title="Skills" icon="fa fa-lightbulb">
        <DynamicForm
          title="Skills"
          self="skills"
          :model="resume.content"
          :schema="schemas.skills"
          :subforms="subforms.skills"
          ></DynamicForm>
      </Tab>
      <Tab title="Awards" icon="fa fa-trophy">
        <DynamicForm
          title="Awards"
          self="awards"
          :model="resume.content"
          :schema="schemas.awards"
          ></DynamicForm>
      </Tab>

    </Tabs>
  </div>
</template>

<script>
import jsonresume from './jsonresume';
import basics from './schema/basics/basics';
import location from './schema/basics/location';
import profiles from './schema/basics/profiles';
import work from './schema/work';
import skills from './schema/skills';
import awards from './schema/awards';
import education from './schema/education';
import Tabs from './tabs/Tabs';
import Tab from './tabs/Tab';
import { component as VueFormGenerator } from 'vue-form-generator';
import 'vue-form-generator/dist/vfg.css';
/* import FieldResumeImage from './vfg/FieldResumeImage'; */
import DynamicForm from './dynamic/DynamicForm';
import ListForm from './dynamic/ListForm';
import Alert from '../reusable/alert.vue';
import route from '../../../../vendor/tightenco/ziggy/src/js';


export default {
  name: "ResumeForm",

  components: {
    VueFormGenerator,
    DynamicForm,
    Tabs,
    Tab,
    Alert
},

  props: {
    update: false,
    resume: {
      type: Object,
      default: () => ({
        id: null,
        title: 'Resume Title',
        content: jsonresume,
      })
    }
  },

  data () {
    return {

      alert: {
        type: 'warning',
        messages: []
      },

      schemas: {
        basics,
        location,
        profiles,
        work,
        education,
        awards,
        skills
      },

      subforms: {
        work: [          
          {
            component: ListForm,
            props: {
              title: 'Highlights',
              self: 'highlights',
              placeholder: 'Started the company'
            } 
          }
        ],

        education: [
          {
           component: ListForm,
            props: {
              title: 'Courses',
              self: 'courses',
              placeholder: 'Db1101 - Basic SQL'
            } 
          }
        ],

        skills: [
          {
           component: ListForm,
            props: {
              title: 'Keywords',
              self: 'keywords',
              placeholder: 'Javascript'
            } 
          }
        ]


      },

      options: {
        validateAfterLoad: true,
        validateAfterChanged: true,
        validateAsync: true,
      },

    };
  },

  methods: {

    validate(target, parent = 'resume') {
      let errors = [];
      for (const [prop, value] of Object.entries(target)) {
        if (Array.isArray(value)) {
          if (value.length === 0) {
            errors.push(`${parent} > ${prop} must have at least one element`);
            continue;
          }
          for (const i in value) {
            if (typeof value[i] === null || value[i] === '') {
              errors.push(`${parent} > ${prop} > ${i} cannot be empty`);
            } else if (typeof value[i] === 'object') {
              errors = errors.concat(
                this.validate(value[i], `${parent} > ${prop} > ${i} `)
              );
            }
          }

        } else if (typeof value === 'object') {
          errors = errors.concat (
            this.validate(value, `${parent} > ${prop} `)
          )
        } else if (value === null || value === '') {
          errors.push(`${parent} > ${prop} is required`)
        }
      }

      return errors;
    },

    isValid() {
      const {alert} = this.$data;
      const {resume} = this.$props;

      alert.messages = [];
      const errors = this.validate(resume.content);
      if (errors.length < 1) {
        return true;
      }

      alert.messages = errors.slice(0, 3);
      if (errors.length > 3) {
        alert.messages.push(
          `<strong>${errors.length - 3} more errors...</strong>`
        )
      }

      alert.type = 'warning';

      return false;
    },
    
    async submit(){

      if (!this.isValid()) {
        return;
      }

      try {
        const res = this.update
          ?await axios.put(route('resumes.update', this.resume.id), this.resume)
          :await axios.post(route('resumes.store'), this.resume);
          
        console.log(res.data);
        window.location= route('resumes.index');
      } catch(e) {
        this.alert.messages = [];
        const errors = e.response.data.errors;
        for (const [prop, value] of Object.entries(errors)) {
          let origin = prop.split('.');
          if (origin[0] === 'content') {
            origin.splice(0, 1);
          }
          origin = origin.join(' > ');
          for (const error of value) {
            const message = error.replace(prop, `<strong>${origin}</strong>`);
            this.alert.messages.push(message);
          }
        }

        this.alert.type = 'danger';

      }
    },
  }
};
</script>
