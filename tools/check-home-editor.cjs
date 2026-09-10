const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const listeners = {};
const formListeners = {};
const sections = Array.from({length:9}, () => ({hidden:false}));
const status = {};
const nav = {children:[],append(item){this.children.push(item)}};
const form = {addEventListener(name, fn){formListeners[name]=fn}};
const root = {
 querySelector(selector){return {'form':form,'.srs-section-nav':nav,'.srs-save-status':status}[selector]},
 querySelectorAll(){return sections}
};
const location = {hash:'#faq'};
vm.runInNewContext(fs.readFileSync('wp-content/themes/suburban-relocation/assets/js/home-editor.js','utf8'), {
 location,
 document:{querySelector(){return root},createElement(){return {attrs:{},setAttribute(k,v){this.attrs[k]=v},removeAttribute(k){delete this.attrs[k]},addEventListener(){}}}},
 window:{addEventListener(name,fn){listeners[name]=fn}}
});
assert.equal(nav.children.length,9);
assert.equal(sections.filter(x=>!x.hidden).length,1);
assert.equal(sections[7].hidden,false);
formListeners.input();
assert.ok(status.textContent);
let prevented = false;
listeners.beforeunload({preventDefault(){prevented=true}});
assert.equal(prevented,true);
location.hash='#hero'; listeners.hashchange();
assert.equal(sections[0].hidden,false);
assert.equal(sections[7].hidden,true);
formListeners.submit(); prevented=false;
listeners.beforeunload({preventDefault(){prevented=true}});
assert.equal(prevented,false);
console.log('PASS: section navigation, FAQ deep link, unsaved changes warning and save.');
