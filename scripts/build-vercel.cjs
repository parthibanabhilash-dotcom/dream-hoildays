'use strict';
const fs=require('fs'),path=require('path');
const root=path.resolve(__dirname,'..'),out=path.join(root,'vercel-static');
fs.mkdirSync(path.join(out,'assets'),{recursive:true});
fs.cpSync(path.join(root,'public/assets'),path.join(out,'assets'),{recursive:true,filter:p=>!fs.statSync(p).isFile()||!(/\.(?:php|phtml|phar|ini|env)$/i.test(p)||path.basename(p).startsWith('.'))});
console.log('Built static assets. PHP requests are routed to the serverless function.');
