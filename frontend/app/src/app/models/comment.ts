import { Post } from "./post";
import { User } from "./user";

export interface Comment {

    id: number;
    content: string;
    author: User;
    post: Post;
    createdAt: Date;
    updatedAt: Date;
}

