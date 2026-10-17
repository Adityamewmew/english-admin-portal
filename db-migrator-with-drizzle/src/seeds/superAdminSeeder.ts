import db from '../db';
import { users } from '../db/schema';
import { eq } from 'drizzle-orm';
import crypto from 'crypto';

const SUPER_ADMIN_EMAIL = process.env.SEED_SUPERADMIN_EMAIL ?? 'admin@eddys.ai';
const SUPER_ADMIN_PASSWORD = process.env.SEED_SUPERADMIN_PASSWORD ?? 'password123';
const SUPER_ADMIN_NAME = process.env.SEED_SUPERADMIN_NAME ?? "Administrator EDDY'S";

export async function superAdminSeeder(): Promise<void> {
  const hash = await Bun.password.hash(SUPER_ADMIN_PASSWORD, {
    algorithm: 'bcrypt',
    cost: 12,
  });
  // PHP Hash::make() uses $2y$ prefix; Bun produces $2b$
  const hashedPassword = hash.replace(/^\$2b\$/, '$2y$');

  const existing = await db
    .select({ id: users.id })
    .from(users)
    .where(eq(users.email, SUPER_ADMIN_EMAIL))
    .limit(1);

  if (existing.length > 0) {
    await db
      .update(users)
      .set({
        name: SUPER_ADMIN_NAME,
        password: hashedPassword,
        role: 'admin',
        accessType: 1,
        currentCefr: 'C2',
        updatedAt: new Date(),
      })
      .where(eq(users.email, SUPER_ADMIN_EMAIL));

    console.log(`[superAdminSeeder] Updated admin account: ${SUPER_ADMIN_EMAIL}`);
  } else {
    await db.insert(users).values({
      id: crypto.randomUUID(),
      name: SUPER_ADMIN_NAME,
      email: SUPER_ADMIN_EMAIL,
      password: hashedPassword,
      role: 'admin',
      accessType: 1,
      currentCefr: 'C2',
      memory: {
        facts: [],
        interests: [],
        weaknesses: [],
        totalCalls: 0,
      },
      createdAt: new Date(),
      updatedAt: new Date(),
    });

    console.log(`[superAdminSeeder] Created super admin: ${SUPER_ADMIN_EMAIL}`);
  }
}
