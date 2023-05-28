const fs = require('fs');
const readline = require('readline');
const { promisify } = require('util');
const { exec } = require('child_process');
const fetch = require('isomorphic-fetch');

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

async function installDependencies() {
  const command = 'npm install isomorphic-fetch';
  await executeCommand(command);
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
  const folderPath = './subdomains';
  fs.mkdirSync(folderPath, { recursive: true });
  fs.writeFileSync(`${folderPath}/${filename}`, [...subdomains].join('\n'));
}

async function main() {
  clearTerminal();
  console.log('DinoSub (Node.js)\nAuthor: https://github.com/daniisaahir\n');

  try {
    await executeCommand('npm list isomorphic-fetch');
  } catch (error) {
    console.log('Required dependencies not found. Installing...');
    await installDependencies();
  }

  const domain = await prompt('Target Domain: ');
  const subdomains = await getSubdomains(domain);
  console.log(`Subdomains found: ${subdomains.size}\n${'-'.repeat(36)}`);
  console.log([...subdomains].join('\n'));

  const saveResults = (await prompt('Save results to txt file? (y/n): ')).toLowerCase();
  if (saveResults === 'y') {
    const filename = await prompt('Filename: ');
    const validFilename = filename.endsWith('.txt') ? filename : `${filename}.txt`;
    saveSubdomainsToFile(subdomains, validFilename);
    console.log(`Saved to subdomains/${validFilename}.`);
  }
}

main()
  .catch((error) => {
    console.error('An error occurred:', error);
    process.exit(1);
  });
