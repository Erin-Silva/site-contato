document.getElementById('form-contato').addEventListener('submit', async (e)=>{
  e.preventDefault();
  const f = new FormData(e.target);
  const res = await fetch('contato.php', { method:'POST', body: f });
  const text = await res.text();
  document.getElementById('resultado').innerHTML = text;
  if(res.ok) e.target.reset();
});
