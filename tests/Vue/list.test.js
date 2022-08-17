import { mount } from '@vue/test-utils';
import ListForm from '../../resources/js/components/resume/dynamic/ListForm.vue';

describe('ListForm Test', () => {
	it('renders empty model', () => {
		const model = {};
		const component = mount(ListForm, {
			propsData: {
				title: 'Test',
				placeholder: 'Test',
				model,
				self: 'test',
			}
		});

		const inputs = component.findAll('inputs');
		expect(inputs.length).toEqual(0);
		expect(model).toEqual({});
	});
});