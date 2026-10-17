import {
  pgTable,
  varchar,
  integer,
  text,
  jsonb,
  timestamp,
  boolean,
  uniqueIndex,
} from 'drizzle-orm/pg-core';

// 1. Users Table
export const users = pgTable('users', {
  id: varchar('id', { length: 36 }).primaryKey(),
  name: varchar('name', { length: 100 }).notNull(),
  email: varchar('email', { length: 100 }).notNull(),
  password: varchar('password', { length: 255 }).notNull(),
  role: varchar('role', { length: 20 }).notNull().default('student'),
  accessType: integer('access_type').notNull().default(2),
  currentCefr: varchar('current_cefr', { length: 10 }).notNull().default('A1'),
  avatarUrl: varchar('avatar_url', { length: 255 }),
  memory: jsonb('memory').default({
    facts: [],
    interests: [],
    weaknesses: [],
    totalCalls: 0,
  }),
  createdAt: timestamp('created_at').defaultNow().notNull(),
  updatedAt: timestamp('updated_at').defaultNow().notNull(),
  deletedAt: timestamp('deleted_at'),
  createdBy: varchar('created_by', { length: 36 }),
  updatedBy: varchar('updated_by', { length: 36 }),
  deletedBy: varchar('deleted_by', { length: 36 }),
});

// 2. Sidebar Menu Groups Table
export const sidebarMenuGroups = pgTable('sidebar_menu_groups', {
  id: integer('id').primaryKey(),
  key: varchar('key', { length: 50 }).notNull().unique(),
  label: varchar('label', { length: 100 }).notNull(),
  color: varchar('color', { length: 50 }).notNull().default('blue'),
  sortOrder: integer('sort_order').notNull().default(0),
  createdAt: timestamp('created_at'),
  updatedAt: timestamp('updated_at'),
});

// 3. Sidebar Menus Table
export const sidebarMenus = pgTable('sidebar_menus', {
  id: integer('id').primaryKey(),
  parentId: integer('parent_id'),
  label: varchar('label', { length: 255 }).notNull(),
  routeName: varchar('route_name', { length: 255 }),
  icon: varchar('icon', { length: 255 }),
  group: varchar('group', { length: 50 }).notNull().default('utama'),
  sortOrder: integer('sort_order').notNull().default(0),
  isActive: integer('is_active').notNull().default(1),
  createdBy: varchar('created_by', { length: 36 }),
  updatedBy: varchar('updated_by', { length: 36 }),
  deletedBy: varchar('deleted_by', { length: 36 }),
  createdAt: timestamp('created_at'),
  updatedAt: timestamp('updated_at'),
  deletedAt: timestamp('deleted_at'),
});

// 4. Sidebar Menu Accesses Table
export const sidebarMenuAccesses = pgTable(
  'sidebar_menu_accesses',
  {
    id: integer('id').primaryKey(),
    sidebarMenuId: integer('sidebar_menu_id').notNull(),
    accessType: integer('access_type').notNull(),
    createdBy: varchar('created_by', { length: 36 }),
    createdAt: timestamp('created_at'),
    updatedAt: timestamp('updated_at'),
  },
  (t) => [
    uniqueIndex('sidebar_menu_accesses_sidebar_menu_id_access_type_unique').on(
      t.sidebarMenuId,
      t.accessType
    ),
  ]
);

// 5. Curriculum Levels Table
export const curriculumLevels = pgTable('curriculum_levels', {
  id: varchar('id', { length: 20 }).primaryKey(),
  cefr: varchar('cefr', { length: 10 }).notNull(),
  title: varchar('title', { length: 150 }).notNull(),
  description: text('description'),
  orderIndex: integer('order_index').notNull().default(1),
  createdAt: timestamp('created_at').defaultNow().notNull(),
});

// 6. Curriculum Modules Table
export const curriculumModules = pgTable('curriculum_modules', {
  id: varchar('id', { length: 20 }).primaryKey(),
  levelId: varchar('level_id', { length: 20 }).notNull().default('A1.1'),
  title: varchar('title', { length: 150 }).notNull(),
  cefr: varchar('cefr', { length: 10 }).notNull(),
  group: varchar('group', { length: 100 }).notNull(),
  objective: text('objective').notNull(),
  complexity: varchar('complexity', { length: 20 }).notNull().default('medium'),
  estimatedMinutes: integer('estimated_minutes').notNull().default(15),
  isExam: boolean('is_exam').notNull().default(false),
  passingScore: integer('passing_score').notNull().default(70),
  orderIndex: integer('order_index').notNull().default(1),
  createdAt: timestamp('created_at').defaultNow().notNull(),
  updatedAt: timestamp('updated_at').defaultNow().notNull(),
  deletedAt: timestamp('deleted_at'),
});

// 7. Sub Modules Table
export const subModules = pgTable('sub_modules', {
  id: varchar('id', { length: 36 }).primaryKey(),
  moduleId: varchar('module_id', { length: 20 }).notNull(),
  skillType: varchar('skill_type', { length: 20 }).notNull(),
  title: varchar('title', { length: 150 }).notNull(),
  orderIndex: integer('order_index').notNull().default(1),
  createdAt: timestamp('created_at').defaultNow().notNull(),
});

// 8. Module Sections Table
export const moduleSections = pgTable('module_sections', {
  id: varchar('id', { length: 36 }).primaryKey(),
  moduleId: varchar('module_id', { length: 20 }).notNull(),
  subModuleId: varchar('sub_module_id', { length: 36 }),
  sectionType: varchar('section_type', { length: 30 }).notNull(),
  title: varchar('title', { length: 150 }).notNull(),
  content: jsonb('content').notNull(),
  orderIndex: integer('order_index').notNull().default(1),
  createdAt: timestamp('created_at').defaultNow().notNull(),
});

// 9. User Module Progress Table
export const userModuleProgress = pgTable('user_module_progress', {
  id: varchar('id', { length: 36 }).primaryKey(),
  userId: varchar('user_id', { length: 36 }).notNull(),
  moduleId: varchar('module_id', { length: 20 }).notNull(),
  status: varchar('status', { length: 20 }).notNull().default('locked'),
  score: integer('score'),
  stepProgress: jsonb('step_progress'),
  completedAt: timestamp('completed_at'),
  createdAt: timestamp('created_at').defaultNow().notNull(),
  updatedAt: timestamp('updated_at').defaultNow().notNull(),
});

// 10. User Sub Module Progress Table
export const userSubModuleProgress = pgTable('user_sub_module_progress', {
  id: varchar('id', { length: 36 }).primaryKey(),
  userId: varchar('user_id', { length: 36 }).notNull(),
  subModuleId: varchar('sub_module_id', { length: 36 }).notNull(),
  status: varchar('status', { length: 20 }).notNull().default('locked'),
  score: integer('score'),
  completedAt: timestamp('completed_at'),
  createdAt: timestamp('created_at').defaultNow().notNull(),
  updatedAt: timestamp('updated_at').defaultNow().notNull(),
});

// 11. Question Bank (Item Bank) Table
export const itemBank = pgTable('item_bank', {
  id: varchar('id', { length: 50 }).primaryKey(),
  skill: varchar('skill', { length: 20 }).notNull(),
  cefr: varchar('cefr', { length: 10 }).notNull(),
  type: varchar('type', { length: 20 }).notNull().default('MCQ'),
  question: text('question').notNull(),
  options: jsonb('options'),
  answer: text('answer').notNull(),
  audioScript: text('audio_script'),
  audioUrl: varchar('audio_url', { length: 255 }),
  explanation: text('explanation'),
  createdAt: timestamp('created_at').defaultNow().notNull(),
  updatedAt: timestamp('updated_at').defaultNow().notNull(),
  deletedAt: timestamp('deleted_at'),
});

// 12. Placement Results Table
export const placementResults = pgTable('placement_results', {
  id: varchar('id', { length: 36 }).primaryKey(),
  userId: varchar('user_id', { length: 36 }).notNull(),
  overallCefr: varchar('overall_cefr', { length: 10 }).notNull(),
  mcqScore: integer('mcq_score').notNull(),
  writingScore: integer('writing_score'),
  speakingScore: integer('speaking_score'),
  scorePercent: integer('score_percent').notNull(),
  details: jsonb('details').notNull(),
  createdAt: timestamp('created_at').defaultNow().notNull(),
});

// 13. Call Sessions Table
export const callSessions = pgTable('call_sessions', {
  id: varchar('id', { length: 36 }).primaryKey(),
  userId: varchar('user_id', { length: 36 }).notNull(),
  moduleId: varchar('module_id', { length: 10 }),
  topic: varchar('topic', { length: 150 }).notNull(),
  durationSeconds: integer('duration_seconds').notNull().default(0),
  transcript: text('transcript'),
  evaluation: jsonb('evaluation'),
  createdAt: timestamp('created_at').defaultNow().notNull(),
});

// 14. Chat Sessions Table
export const chatSessions = pgTable('chat_sessions', {
  id: varchar('id', { length: 36 }).primaryKey(),
  userId: varchar('user_id', { length: 36 }).notNull(),
  title: varchar('title', { length: 200 }).notNull().default('Percakapan Baru'),
  createdAt: timestamp('created_at').defaultNow().notNull(),
  updatedAt: timestamp('updated_at').defaultNow().notNull(),
});

// 15. Chat Messages Table
export const chatMessages = pgTable('chat_messages', {
  id: varchar('id', { length: 36 }).primaryKey(),
  sessionId: varchar('session_id', { length: 36 }).notNull(),
  role: varchar('role', { length: 20 }).notNull(),
  content: text('content').notNull(),
  metadata: jsonb('metadata'),
  createdAt: timestamp('created_at').defaultNow().notNull(),
});
