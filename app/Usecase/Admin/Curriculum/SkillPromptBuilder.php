<?php

namespace App\Usecase\Admin\Curriculum;

class SkillPromptBuilder
{
    public function build(string $skill, object $module, string $customInstructions = '', ?string $docText = null): array
    {
        return match (strtolower($skill)) {
            'reading' => $this->buildReadingPrompt($module, $customInstructions, $docText),
            'listening' => $this->buildListeningPrompt($module, $customInstructions, $docText),
            'writing' => $this->buildWritingPrompt($module, $customInstructions, $docText),
            'speaking' => $this->buildSpeakingPrompt($module, $customInstructions, $docText),
            default => throw new \InvalidArgumentException("Unsupported skill prompt: {$skill}"),
        };
    }

    private function getCleanSlug(string $id): string
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $id));
    }

    private function buildReadingPrompt(object $module, string $customInstructions, ?string $docText): array
    {
        $slug = $this->getCleanSlug($module->id);
        $cefr = strtoupper($module->cefr ?? 'A1');

        $system = <<<PROMPT
You are an expert Cambridge & Oxford CEFR {$cefr} reading curriculum author for EDDY'S English School.
Generate the complete 5-section 'reading' sub-module for module ID {$module->id}: "{$module->title}".

CRITICAL MANDATES:
1. ZERO AI SLOP: Absolutely avoid generic buzzwords (delve, testament, unlock, elevate, journey, robust, seamless, tapestry).
2. NO EM DASHES: Do not use the em dash character (—). Use a comma, colon, or separate sentences instead.
3. 100% NATURAL ENGLISH: All passages, options, and explanations must be in natural CEFR {$cefr} English.
4. INDONESIAN ONLY IN VOCAB MEANING: Only 'meaning' in vocabulary items may be in Indonesian.

Return ONLY a valid JSON array of exactly 5 sections:
[
  {
    "id": "sec-{$slug}-r-theory",
    "sub_module_id": "sub-{$slug}-reading",
    "section_type": "theory",
    "title": "Learning Objectives & Warm-up",
    "order_index": 1,
    "content": {
      "summary": "Clear, friendly pedagogical overview in English...",
      "linguisticTarget": {
        "grammar": "Core grammar patterns in English...",
        "vocabulary": "Primary vocabulary topics in English...",
        "readingStrategy": "Reading strategy in English (skimming for main ideas, scanning for details)..."
      },
      "learningObjectives": ["Understand the main idea of short {$cefr} texts", "Locate specific details", "Recognize grammar patterns"],
      "contextWarmup": { "prompt": "Engaging warm-up question...", "options": ["Option 1", "Option 2", "Option 3"], "correctIndex": 0, "explanation": "..." },
      "rules": ["Rule 1 in English", "Rule 2 in English", "Rule 3 in English"],
      "commonTrap": { "trapTitle": "Common Learner Pitfall", "wrong": "Incorrect sentence", "correct": "Correct pattern", "explanation": "..." },
      "contractions": [{ "subject": "He", "verb": "is", "contracted": "He's", "exampleFull": "He is a student.", "exampleContracted": "He's a student." }],
      "grammarFormulas": { "title": "Sentence Patterns", "description": "Formula explanation", "subjects": [{"label": "I", "pronoun": "I"}], "verbs": [{"base": "read"}], "complements": [{"text": "books every day"}] }
    }
  },
  {
    "id": "sec-{$slug}-r-vocab",
    "sub_module_id": "sub-{$slug}-reading",
    "section_type": "vocab",
    "title": "Key Vocabulary Bank",
    "order_index": 2,
    "content": {
      "items": [
        { "word": "collect", "ipa": "/kəˈlekt/", "meaning": "mengumpulkan", "partOfSpeech": "verb", "exampleSentence": "I collect coins.", "collocation": "collect coins" }
      ]
    }
  },
  {
    "id": "sec-{$slug}-r-guided",
    "sub_module_id": "sub-{$slug}-reading",
    "section_type": "dialogue",
    "title": "Guided Reading Passage",
    "order_index": 3,
    "content": {
      "passageTitle": "Passage Title in English",
      "audioUrl": "/audio/modules/{$slug}/reading-passage.mp3",
      "durationSeconds": 60,
      "paragraphs": [
        "Paragraph 1 in natural CEFR {$cefr} English (50-70 words)...",
        "Paragraph 2 in natural CEFR {$cefr} English (50-70 words)..."
      ],
      "timestamps": []
    }
  },
  {
    "id": "sec-{$slug}-r-practice",
    "sub_module_id": "sub-{$slug}-reading",
    "section_type": "practice",
    "title": "Interactive Cable Connection (Vocab Match)",
    "order_index": 4,
    "content": {
      "drills": [
        {
          "id": "drill-cable-1",
          "title": "Interactive Cable Connection (Vocab Match)",
          "instruction": "Connect each vocabulary term on the left with its accurate meaning on the right.",
          "pairs": [
            { "left": "collect", "right": "mengumpulkan" }
          ]
        }
      ]
    }
  },
  {
    "id": "sec-{$slug}-r-quiz",
    "sub_module_id": "sub-{$slug}-reading",
    "section_type": "quiz",
    "title": "Reading Comprehension Checkpoint",
    "order_index": 5,
    "content": {
      "passingScore": 80,
      "questions": [
        { "id": "q-r-1", "question": "Comprehension question in English...", "options": ["Option A", "Option B", "Option C"], "answer": "Option A", "explanation": "..." }
      ]
    }
  }
]

No markdown code fences. Provide exactly 8-10 vocabulary items in vocab, 5 matching pairs in practice drills (drawn directly from vocab items), and 5 focused reading comprehension questions in quiz.
PROMPT;

        $user = "Generate Reading submodule for Module ID {$module->id}, Title: {$module->title}, CEFR: {$cefr}. Objective: {$module->objective}. {$customInstructions}";
        if ($docText) {
            $user .= "\n\n[REFERENCE TEXT]:\n".substr($docText, 0, 35000);
        }

        return ['system' => $system, 'user' => $user];
    }

    private function buildListeningPrompt(object $module, string $customInstructions, ?string $docText): array
    {
        $slug = $this->getCleanSlug($module->id);
        $cefr = strtoupper($module->cefr ?? 'A1');

        $system = <<<PROMPT
You are an expert Cambridge & Oxford CEFR {$cefr} listening curriculum designer for EDDY'S English School.
Generate the complete 3-section 'listening' sub-module for module ID {$module->id}: "{$module->title}".
CRITICAL: Listening has EXACTLY 3 sections (theory, dialogue, quiz). Do NOT generate separate vocab or practice sections!

CRITICAL MANDATES:
1. ZERO AI SLOP & NO EM DASHES.
2. 100% NATURAL SPOKEN ENGLISH: Dialogue lines must sound authentic, crisp, and natural (3-8 words per line).
3. INDONESIAN ONLY IN MEANING & TRANSLATION: Only keyPhrases 'meaning' and dialogue lines 'translation' may be in Indonesian.
4. NO MULTIPLE CHOICE: In sec-{$slug}-l-quiz, you MUST generate 10 interactive listening tasks:
   - 5 Dictation tasks (type: 'dictation'): Student listens to audio clip and types the missing word in the blank.
   - 5 Sentence Arrange tasks (type: 'arrange'): Student listens to audio clip and arranges the jumbled words into correct order.

Return ONLY a valid JSON array of exactly 3 sections:
[
  {
    "id": "sec-{$slug}-l-theory",
    "sub_module_id": "sub-{$slug}-listening",
    "section_type": "theory",
    "title": "Audio Briefing & Ear Prep",
    "order_index": 1,
    "content": {
      "summary": "Listening strategy briefing in English...",
      "learningObjectives": ["Catch main topic of spoken conversation", "Identify specific details", "Notice connected speech"],
      "keyPhrases": [
        { "phrase": "Where are you from?", "meaning": "Dari mana kamu berasal?", "note": "Common casual greeting" }
      ],
      "connectedSpeech": [
        { "id": 1, "slow": "Where are you from?", "fast": "Where d'you come from?", "slowIpa": "/weər ɑːr juː frɒm/", "fastIpa": "/weə djə kʌm frɒm/", "note": "Vowel reduction in rapid speech" }
      ]
    }
  },
  {
    "id": "sec-{$slug}-l-dialogue",
    "sub_module_id": "sub-{$slug}-listening",
    "section_type": "dialogue",
    "title": "Spoken Audio Dialogue",
    "order_index": 2,
    "content": {
      "context": "Context and scene setting in English...",
      "audioPrompt": "Listen to the conversation between two people.",
      "lines": [
        { "speaker": "Speaker 1", "text": "Hello, my name is Alex.", "translation": "Halo, nama saya Alex.", "emotion": "friendly" }
      ]
    }
  },
  {
    "id": "sec-{$slug}-l-quiz",
    "sub_module_id": "sub-{$slug}-listening",
    "section_type": "quiz",
    "title": "Audio Listening Practicum (10 Interactive Tasks)",
    "order_index": 3,
    "content": {
      "passingScore": 80,
      "practicumQuestions": [
        {
          "id": 1,
          "type": "dictation",
          "audioText": "It is a new shirt.",
          "instruction": "Listen to the audio clip and type the missing word:",
          "promptSentence": "It is a new _____.",
          "targetWord": "shirt",
          "explanation": "Full sentence: 'It is a new shirt.' The missing word is 'shirt'."
        },
        {
          "id": 6,
          "type": "arrange",
          "audioText": "She is from Spain.",
          "instruction": "Listen to the sentence and arrange the words in the correct order:",
          "targetSentence": "She is from Spain.",
          "words": ["from", "is", "Spain.", "She"],
          "explanation": "Subject (She) + Verb (is) + Preposition (from) + Country (Spain)."
        }
      ]
    }
  }
]

No markdown code fences. Provide 4 keyPhrases, 4 connectedSpeech examples, 10-12 dialogue lines, and exactly 10 practicumQuestions in quiz (5 dictation items with id 1-5, and 5 arrange items with id 6-10 drawn from the dialogue).
PROMPT;

        $user = "Generate Listening submodule for Module ID {$module->id}, Title: {$module->title}, CEFR: {$cefr}. Objective: {$module->objective}. {$customInstructions}";
        if ($docText) {
            $user .= "\n\n[REFERENCE TEXT]:\n".substr($docText, 0, 35000);
        }

        return ['system' => $system, 'user' => $user];
    }

    private function buildWritingPrompt(object $module, string $customInstructions, ?string $docText): array
    {
        $slug = $this->getCleanSlug($module->id);
        $cefr = strtoupper($module->cefr ?? 'A1');

        $system = <<<PROMPT
You are an expert Cambridge & Oxford CEFR {$cefr} writing coach for EDDY'S English School.
Generate the complete 3-section 'writing' sub-module for module ID {$module->id}: "{$module->title}".
CRITICAL: Writing has EXACTLY 3 sections (theory, practice, quiz). Do NOT generate separate vocab or dialogue sections!

CRITICAL MANDATES:
1. ZERO AI SLOP & NO EM DASHES.
2. 100% NATURAL ENGLISH. Indonesian allowed only in translations/meanings.
3. NO MULTIPLE CHOICE: In sec-{$slug}-w-quiz, you MUST generate 10 authentic typing questions (practicumQuestions):
   - Sentence transformations (affirmative to negative, question form).
   - Conjunction joins ('and', 'but', 'because').
   - Capitalisation & punctuation mechanics correction.
   - Target full sentence answers.

Return ONLY a valid JSON array of exactly 3 sections:
[
  {
    "id": "sec-{$slug}-w-theory",
    "sub_module_id": "sub-{$slug}-writing",
    "section_type": "theory",
    "title": "Sentence Formulas & Word Order",
    "order_index": 1,
    "content": {
      "summary": "Practical guide on sentence building in English...",
      "blueprintItems": [
        { "step": 1, "title": "Core Sentence Formula", "formula": "Subject + Verb + Object", "example": "Emma plays tennis on Sundays." }
      ],
      "mechanicsRules": [
        { "rule": "Always capitalize names of people, countries, and days.", "badExample": "we live in london.", "goodExample": "We live in London.", "why": "Proper nouns require capitals." }
      ],
      "sampleSentences": [
        { "en": "I usually tidy my bedroom on Saturdays.", "id": "Saya biasanya merapikan kamar pada hari Sabtu.", "focus": "Adverb position" }
      ]
    }
  },
  {
    "id": "sec-{$slug}-w-practice",
    "sub_module_id": "sub-{$slug}-writing",
    "section_type": "practice",
    "title": "Sentence Building & Guided Writing Practice",
    "order_index": 2,
    "content": {
      "mechanicsChallenges": [
        { "id": "sent-1", "faultySentence": "ryan dont like tennis on mondays", "targetSentence": "Ryan doesn't like tennis on Mondays.", "translation": "Ryan tidak suka tenis pada hari Senin.", "keyRule": "Capitalize names and days.", "requiredCapitals": ["Ryan", "Mondays"] }
      ],
      "sentenceBuilderTargets": [
        { "id": "b-1", "category": "Daily Routine", "meaning": "Saya suka bermain gitar.", "tokens": ["I", "like", "playing", "the", "guitar."], "fullSentence": "I like playing the guitar." }
      ],
      "guidedLabChallenges": [
        { "id": "ch-1", "title": "Challenge 1: Introduce Yourself", "badge": "Young Writer", "instruction": "Write three complete sentences about your name, country, and favorite hobby.", "example": "My name is John. I am from Spain. I love playing football.", "placeholder": "Start typing here...", "minWords": 12, "xpReward": 35 }
      ]
    }
  },
  {
    "id": "sec-{$slug}-w-quiz",
    "sub_module_id": "sub-{$slug}-writing",
    "section_type": "quiz",
    "title": "Writing Mastery Practicum (10 Authentic Tasks)",
    "order_index": 3,
    "content": {
      "passingScore": 75,
      "practicumQuestions": [
        {
          "id": 1,
          "typeLabel": "Sentence Transformation",
          "instruction": "Change the following affirmative sentence into a complete negative sentence:",
          "promptText": "Liam is from Melbourne.",
          "hint": "Use 'is not' or the contraction 'isn't'.",
          "targetAnswers": ["Liam is not from Melbourne.", "Liam isn't from Melbourne."],
          "explanation": "The negative form of 'Liam is' is 'Liam is not' or 'Liam isn't'."
        }
      ]
    }
  }
]

No markdown code fences. Provide 3 blueprintItems, 3 mechanicsRules, 3 sampleSentences, 3 mechanicsChallenges, 3 sentenceBuilderTargets, 2 guidedLabChallenges, and exactly 10 practicumQuestions in quiz.
PROMPT;

        $user = "Generate Writing submodule for Module ID {$module->id}, Title: {$module->title}, CEFR: {$cefr}. Objective: {$module->objective}. {$customInstructions}";
        if ($docText) {
            $user .= "\n\n[REFERENCE TEXT]:\n".substr($docText, 0, 35000);
        }

        return ['system' => $system, 'user' => $user];
    }

    private function buildSpeakingPrompt(object $module, string $customInstructions, ?string $docText): array
    {
        $slug = $this->getCleanSlug($module->id);
        $cefr = strtoupper($module->cefr ?? 'A1');

        $system = <<<PROMPT
You are an expert Cambridge & Oxford CEFR {$cefr} speaking curriculum designer for EDDY'S English School.
Generate the complete 3-section 'speaking' sub-module for module ID {$module->id}: "{$module->title}".
CRITICAL: Speaking has EXACTLY 3 sections (theory, practice, quiz). Do NOT generate separate vocab or dialogue sections!

CRITICAL MANDATES:
1. ZERO AI SLOP & NO EM DASHES.
2. 100% NATURAL SPOKEN ENGLISH.
3. NO MULTIPLE CHOICE: In sec-{$slug}-s-quiz, you MUST generate 10 oral assessment tasks (practicumQuestions):
   - Questions 1-2: category 'storytelling' (free spoken monologue for 20-40s with inspirationPoints, sampleStarter, expectedKeywords).
   - Questions 3-6: category 'roleplay' (interactive voice call scenario with Mr. Khoirul with scenarioContext, characterRole, suggestedTopic, minTurns).
   - Questions 7-10: category 'linguistic_drill' (pronunciation stress, intonation, speech reflex).

Return ONLY a valid JSON array of exactly 3 sections:
[
  {
    "id": "sec-{$slug}-s-theory",
    "sub_module_id": "sub-{$slug}-speaking",
    "section_type": "theory",
    "title": "Pronunciation & Intonation Blueprint",
    "order_index": 1,
    "content": {
      "summary": "Speaking and articulation overview in English...",
      "audioGuidance": "Pronunciation guidance in English...",
      "pillars": [
        { "name": "Vowel Clarity", "target": "Clear short and long vowel sounds", "explanation": "Explanation in English..." }
      ]
    }
  },
  {
    "id": "sec-{$slug}-s-practice",
    "sub_module_id": "sub-{$slug}-speaking",
    "section_type": "practice",
    "title": "Oral Drills, Syllable Shift & Roleplay",
    "order_index": 2,
    "content": {
      "stressPairs": [
        { "id": "pair-1", "country": "CA-na-da", "countryAudio": "canada", "countrySyllables": ["CA", "na", "da"], "nationality": "ca-NA-di-an", "nationalityAudio": "canadian", "nationalitySyllables": ["ca", "NA", "di", "an"], "ruleNote": "Stress shifts to the second syllable in the nationality." }
      ],
      "drills": [
        { "id": "spk-d-1", "targetText": "Hello! My name is Alex.", "phonetic": "/həˈloʊ maɪ neɪm ɪz ˈæl.ɪks/", "focus": "Greeting & Name", "hint": "Speak clearly with friendly intonation." }
      ],
      "roleplay": {
        "context": "You meet Mr. Khoirul in the school courtyard during break time.",
        "roles": ["Mr. Khoirul", "Student"],
        "turns": [
          { "speaker": "Mr. Khoirul", "text": "Hi there! Where are you from?", "translation": "Halo! Dari mana kamu berasal?" },
          { "speaker": "Student", "text": "I am from Indonesia. What about you?", "translation": "Saya berasal dari Indonesia. Bagaimana dengan Anda?" }
        ]
      }
    }
  },
  {
    "id": "sec-{$slug}-s-quiz",
    "sub_module_id": "sub-{$slug}-speaking",
    "section_type": "quiz",
    "title": "Speaking Practicum (10 Oral Assessments)",
    "order_index": 3,
    "content": {
      "passingScore": 70,
      "practicumQuestions": [
        {
          "id": 1,
          "category": "storytelling",
          "typeLabel": "Personal Storytelling",
          "instruction": "Speak for 20 to 40 seconds about yourself, where you live, and your favorite activity.",
          "audioPrompt": "Hello! Please tell me about yourself and what you like to do.",
          "inspirationPoints": ["Idea 1: Your name and city", "Idea 2: What you do in your free time"],
          "sampleStarter": "Hello, my name is... I am from...",
          "expectedKeywords": ["name", "from", "I am", "like"],
          "minSeconds": 15,
          "hint": "Speak naturally as if talking to a friendly teacher."
        },
        {
          "id": 3,
          "category": "roleplay",
          "typeLabel": "Interactive Voice Call with Mr. Khoirul",
          "instruction": "Have a spoken conversation with Mr. Khoirul. Answer his questions and ask him about his hobbies.",
          "audioPrompt": "Hello! Welcome to our school. Nice to meet you!",
          "scenarioContext": "Meeting Mr. Khoirul at the registration desk",
          "characterRole": "Friendly English Tutor",
          "minTurns": 3
        },
        {
          "id": 7,
          "category": "linguistic_drill",
          "typeLabel": "Syllable Stress Reflex",
          "instruction": "Listen to the word stress and repeat clearly:",
          "audioPrompt": "Repeat: Canada. Canadian.",
          "targetPhrase": "Canada. Canadian.",
          "phonetic": "/ˈkænədə/ /kəˈneɪdiən/",
          "pitchPattern": "Stress shift"
        }
      ]
    }
  }
]

No markdown code fences. Provide 4 stressPairs, 4 drills, 4 roleplay turns, and exactly 10 practicumQuestions in quiz (2 storytelling, 4 roleplay, 4 linguistic drills).
PROMPT;

        $user = "Generate Speaking submodule for Module ID {$module->id}, Title: {$module->title}, CEFR: {$cefr}. Objective: {$module->objective}. {$customInstructions}";
        if ($docText) {
            $user .= "\n\n[REFERENCE TEXT]:\n".substr($docText, 0, 35000);
        }

        return ['system' => $system, 'user' => $user];
    }
}
