const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const code = fs.readFileSync('wp-content/themes/suburban-relocation/assets/js/quote-forms.js', 'utf8');
function setup(responses) {
  const button = {disabled:false, hidden:false, style:{}};
  const feedback = {classList:{add(){},remove(){}},focus(){this.focused=true}};
  const grid = {hidden:false,style:{}};
  const date = {};
  let handler;
  const attrs = {};
  const calls = [];
  const form = {
    dataset:{ajaxUrl:'https://example.com/wp-admin/admin-ajax.php'},
    fields:{phone:'2025550100',full_name:"O'Neil",action:'srs_submit_quote'},
    querySelector(selector){return {'[type="submit"]':button,'.srs-quote-feedback':feedback,'[name="move_date"]':date,'.srs-form-grid':grid}[selector]},
    addEventListener(event,fn){handler=fn},reportValidity(){return true},
    setAttribute(k,v){attrs[k]=v},removeAttribute(k){delete attrs[k]}
  };
  vm.runInNewContext(code, {
    document:{querySelectorAll(){return [form]}}, Date, URLSearchParams,
    FormData:class {constructor(form){this.fields={...form.fields}}set(k,v){this.fields[k]=v}},
    fetch:async(url,args)=>{calls.push({url,args}); const next=responses.shift(); if(next instanceof Error)throw next; return typeof next==='function'?await next():next}
  });
  return {button,feedback,grid,date,form,attrs,calls,submit:()=>handler({preventDefault(){}})};
}
const response = (ok,payload)=>({ok,json:async()=>payload});
const nonce = ()=>response(true,{success:true,data:{nonce:'fresh-nonce'}});
(async()=>{
  const success=setup([nonce(),response(true,{success:true,data:{message:'Saved'}})]);
  await success.submit();
  assert.equal(success.calls[1].args.body.fields.srs_quote_nonce,'fresh-nonce');
  assert.equal(success.grid.style.display,'none');
  assert.equal(success.button.style.display,'none');
  assert.equal(success.feedback.textContent,'Saved');
  assert.equal(success.attrs['aria-busy'],undefined);
  const invalid=setup([nonce(),response(false,{success:false,data:{message:'Invalid email'}})]);
  await invalid.submit();
  assert.equal(invalid.form.fields.full_name,"O'Neil");
  assert.equal(invalid.grid.hidden,false);
  assert.equal(invalid.button.disabled,false);
  assert.equal(invalid.feedback.textContent,'Invalid email');
  const network=setup([nonce(),new Error('network')]);
  await network.submit();
  assert.equal(network.grid.hidden,false);
  assert.match(network.feedback.textContent,/could not confirm/);
  let release;
  const pending=setup([()=>new Promise(resolve=>{release=()=>resolve(nonce())}),response(true,{success:true,data:{message:'Saved'}})]);
  const first=pending.submit();
  await pending.submit();
  assert.equal(pending.calls.length,1);
  assert.equal(pending.button.disabled,true);
  release(); await first;
  assert.equal(pending.calls.length,2);
  console.log('PASS: fresh nonce, success, validation error, network failure, input preservation and double-click protection.');
})().catch(error=>{console.error(error);process.exitCode=1});
