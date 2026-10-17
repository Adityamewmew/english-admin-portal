import db from '../db';
import { sidebarMenuAccesses, sidebarMenuGroups, sidebarMenus } from '../db/schema';
import { eq, and } from 'drizzle-orm';

export async function sidebarSeeder(): Promise<void> {
  // 1. Seed Groups
  const groups = [
    {
      id: 1,
      key: 'utama',
      label: 'Utama',
      color: 'blue',
      sortOrder: 1,
      createdAt: new Date(),
      updatedAt: new Date(),
    },
    {
      id: 2,
      key: 'akademik',
      label: 'Curriculum & Learning',
      color: 'emerald',
      sortOrder: 2,
      createdAt: new Date(),
      updatedAt: new Date(),
    },
    {
      id: 3,
      key: 'sistem',
      label: 'System Settings',
      color: 'indigo',
      sortOrder: 3,
      createdAt: new Date(),
      updatedAt: new Date(),
    },
  ];

  for (const group of groups) {
    const existing = await db
      .select({ id: sidebarMenuGroups.id })
      .from(sidebarMenuGroups)
      .where(eq(sidebarMenuGroups.key, group.key))
      .limit(1);

    if (existing.length > 0) {
      await db
        .update(sidebarMenuGroups)
        .set(group)
        .where(eq(sidebarMenuGroups.key, group.key));
      console.log(`[sidebarSeeder] Updated group: ${group.key}`);
    } else {
      await db.insert(sidebarMenuGroups).values(group);
      console.log(`[sidebarSeeder] Created group: ${group.key}`);
    }
  }

  // 2. Seed Menus
  // Remove old standalone menu 4 if exists
  await db.delete(sidebarMenuAccesses).where(eq(sidebarMenuAccesses.sidebarMenuId, 4));
  await db.delete(sidebarMenus).where(eq(sidebarMenus.id, 4));

  const menus = [
    {
      id: 1,
      parentId: null,
      label: 'Dashboard',
      routeName: 'admin.dashboard',
      icon: '_admin._layout.icons.sidebar.dashboard',
      group: 'utama',
      sortOrder: 1,
      isActive: 1,
      createdAt: new Date(),
      updatedAt: new Date(),
    },
    {
      id: 2,
      parentId: null,
      label: 'Users',
      routeName: 'admin.users.index',
      icon: '_admin._layout.icons.sidebar.user',
      group: 'utama',
      sortOrder: 2,
      isActive: 1,
      createdAt: new Date(),
      updatedAt: new Date(),
    },
    {
      id: 3,
      parentId: null,
      label: 'Curriculum Management',
      routeName: null,
      icon: '_admin._layout.icons.sidebar.data_master',
      group: 'akademik',
      sortOrder: 1,
      isActive: 1,
      createdAt: new Date(),
      updatedAt: new Date(),
    },
    {
      id: 301,
      parentId: 3,
      label: 'Modules',
      routeName: 'admin.modules.index',
      icon: null,
      group: 'akademik',
      sortOrder: 1,
      isActive: 1,
      createdAt: new Date(),
      updatedAt: new Date(),
    },
    {
      id: 302,
      parentId: 3,
      label: 'Sub Modules',
      routeName: 'admin.sub_modules.index',
      icon: null,
      group: 'akademik',
      sortOrder: 2,
      isActive: 1,
      createdAt: new Date(),
      updatedAt: new Date(),
    },
    {
      id: 303,
      parentId: 3,
      label: 'Placement Test',
      routeName: 'admin.questions.index',
      icon: null,
      group: 'akademik',
      sortOrder: 3,
      isActive: 1,
      createdAt: new Date(),
      updatedAt: new Date(),
    },
    {
      id: 5,
      parentId: null,
      label: 'Menu Settings',
      routeName: 'admin.sidebar_menu.index',
      icon: '_admin._layout.icons.sidebar.settings',
      group: 'sistem',
      sortOrder: 1,
      isActive: 1,
      createdAt: new Date(),
      updatedAt: new Date(),
    },
  ];

  for (const menu of menus) {
    const existing = await db
      .select({ id: sidebarMenus.id })
      .from(sidebarMenus)
      .where(eq(sidebarMenus.id, menu.id))
      .limit(1);

    if (existing.length > 0) {
      await db
        .update(sidebarMenus)
        .set(menu)
        .where(eq(sidebarMenus.id, menu.id));
      console.log(`[sidebarSeeder] Updated menu: ${menu.label}`);
    } else {
      await db.insert(sidebarMenus).values(menu);
      console.log(`[sidebarSeeder] Created menu: ${menu.label}`);
    }

    const accessExisting = await db
      .select({ id: sidebarMenuAccesses.id })
      .from(sidebarMenuAccesses)
      .where(
        and(
          eq(sidebarMenuAccesses.sidebarMenuId, menu.id),
          eq(sidebarMenuAccesses.accessType, 1)
        )
      )
      .limit(1);

    if (accessExisting.length === 0) {
      await db.insert(sidebarMenuAccesses).values({
        id: menu.id,
        sidebarMenuId: menu.id,
        accessType: 1,
        createdAt: new Date(),
      });
      console.log(`[sidebarSeeder] Granted access for menu ${menu.id} to admin`);
    }
  }
}
