const fs = require('fs');
const readline = require('readline');
const { promisify } = require('util');
const { exec } = require('child_process');
const fetch = require('isomorphic-fetch');
const path = require('path');

const executeCommand = promisify(exec);

function clearTerminal() {
  process.stdout.write('\x1Bc');
}

function prompt(question) {
  const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
  });

  return new Promise((resolve) => {
    rl.question(question, (answer) => {
      rl.close();
      resolve(answer);
    });
  });
}

async function getSubdomains(domain) {
  const response = await fetch(`https://crt.sh/?q=%.${domain}&output=json`);
  if (response.ok) {
    const data = await response.json();
    return new Set(data.map((entry) => entry.name_value));
  }
  return new Set();
}

function saveSubdomainsToFile(subdomains, filename) {
  const folderPath = path.join(__dirname, 'subdomains');
  fs.mkdirSync(folderPath, { recursive: true });
  const filePath = path.join(folderPath, filename);
  fs.writeFileSync(filePath, [...subdomains].join('\n'));
  return path.resolve(filePath);
}

async function main() {
  clearTerminal();
  console.log('DinoSub (Node.js) v1.2.1\nAuthor: https://github.com/daniisaahir\n');

  const domain = await prompt('Target Domain: ');
  const subdomains = await getSubdomains(domain);
  console.log(`Subdomains found: ${subdomains.size}\n${'-'.repeat(36)}`);
  console.log([...subdomains].join('\n'));

  const saveResults = (await prompt('Save results to txt file? (y/n): ')).toLowerCase();
  if (saveResults === 'y') {
    const filename = await prompt('Filename: ');
    const validFilename = filename.endsWith('.txt') ? filename : `${filename}.txt`;
    const savedFilePath = saveSubdomainsToFile(subdomains, validFilename);
    console.log(`Saved to: ${savedFilePath}`);
  }
}

main()
  .catch((error) => {
    console.error('An error occurred:', error);
    process.exit(1);
  });
