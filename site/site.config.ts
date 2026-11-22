// Per-repo config for the shared docs-theme (repo standards §7).

interface SocialLink {
  kind: 'github' | 'linkedin';
  label: string;
  href: string;
}

const repo = 'https://github.com/Stravelakis/DogPark-Weather-WPPlugin-TELEX';

// "Try it live": WordPress Playground (playground.wordpress.net) runs a real
// WordPress in the visitor's browser. This blueprint installs the released
// plugin straight from the v0.12repo tag, adds two example parks and a page
// with the block on it. It is the real plugin, not a mock-up.
const demoPhp = `<?php require '/wordpress/wp-load.php';
update_option('telex_dpw_parks', [
 ['id'=>'riverside','name'=>'Riverside Dog Park','lat'=>'37.9838','lon'=>'23.7275','drainage'=>true,'shade'=>true,'wind_shelter'=>false,'covered'=>false],
 ['id'=>'hilltop','name'=>'Hilltop Meadow','lat'=>'37.9715','lon'=>'23.7257','drainage'=>false,'shade'=>false,'wind_shelter'=>true,'covered'=>false]
]);
wp_insert_post(['post_title'=>'Dog Park Weather','post_name'=>'dpw-demo','post_status'=>'publish','post_type'=>'page','post_content'=>'<!-- wp:telex/block-telex-dog-park-weather {"parkId":"riverside"} /-->']);`;

const demoBlueprint = {
  landingPage: '/dpw-demo/',
  preferredVersions: { php: '8.2', wp: 'latest' },
  features: { networking: true },
  login: true,
  steps: [
    {
      step: 'installPlugin',
      pluginData: { resource: 'git:directory', url: repo, ref: 'v0.12repo', refType: 'tag', path: '/' },
      options: { activate: true, targetFolderName: 'telex-dog-park-weather' },
    },
    { step: 'runPHP', code: demoPhp },
  ],
};

export const demoUrl = 'https://playground.wordpress.net/#' + encodeURIComponent(JSON.stringify(demoBlueprint));

export const siteConfig = {
  projectName: 'Dog Park Weather Rating',
  description:
    'A WordPress block that turns live hourly weather into a 1 to 5 paw rating for any dog park, in any language. No API key needed.',
  repoUrl: repo,
  faviconHref: '/DogPark-Weather-WPPlugin-TELEX/favicon.svg',

  seoTitle: 'Dog Park Weather Rating: a 1 to 5 paw score for any dog park, in WordPress',
  socialImage: 'og-image.png',
  socialImageAlt: 'Dog Park Weather Rating: a WordPress block that turns hourly weather into a paw rating.',
  locale: 'en_GB',

  // First visit lands on English; a reader's saved choice still wins.
  defaultVernacular: 'plain',

  // Left menu, top group. The "On this page" group below it is built from the
  // headings of the reading level on screen.
  nav: [
    { id: 'top', label: 'Overview' },
    { id: 'features', label: 'What it does' },
    { id: 'showcase', label: 'Screenshots' },
  ],

  // Buttons and chips in the hero.
  heroLinks: [
    { label: 'Try it live', href: demoUrl, primary: true },
    { label: 'Get the plugin', href: `${repo}/releases/latest` },
    { label: 'Source on GitHub', href: repo },
    { label: 'What changed', href: `${repo}/blob/main/readme.txt` },
  ],
  heroPills: ['No API key', 'Free weather source', 'Any language', 'Clean uninstall'],

  // The three cards under the hero. They switch the reading level.
  levelCards: [
    { id: 'dev', tag: 'Dev', title: 'How it is built', text: 'Block, REST routes, weather providers, the exact rating maths, caching and cron.' },
    { id: 'plain', tag: 'English', title: 'How to use it', text: 'For site owners who are comfortable with tech but do not write code. Install, add a park, add the block, fix problems.' },
    { id: 'eli5', tag: 'ELI5', title: 'Start from zero', text: 'What WordPress, a plugin and a block are, then every click from install to your first paw rating.' },
  ],

  vernaculars: [
    { id: 'dev', label: 'Dev' },
    { id: 'plain', label: 'English' },
    { id: 'eli5', label: 'ELI5' },
  ],

  // The plugin is PHP and runs inside WordPress, so a browser code sandbox
  // would be a fake. The "Try it live" button opens a real WordPress instead
  // (WordPress Playground); the docs also walk through it with real
  // screenshots. Never a fake "run" button.
  playground: {
    enabled: false,
    starterCode: '',
  },

  feedbackUrl: '',

  author: {
    name: 'Stravelakis',
    links: [
      { kind: 'github', label: 'GitHub', href: 'https://github.com/Stravelakis' },
      { kind: 'linkedin', label: 'LinkedIn', href: 'https://www.linkedin.com/in/stravelakiscom/' },
    ] as SocialLink[],
  },
};
